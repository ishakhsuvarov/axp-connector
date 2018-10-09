<?php

namespace Adobe\AxpConnector\Model\Config\Backend;

class LaunchScriptUrl extends \Magento\Framework\App\Config\Value
{

  // Did they paste the script tag?
  // Example: <script src="//assets.adobedtm.com/launch-EN5ea69d1bda314cdeacd364e2fXXXXXXX-development.min.js" async></script>
  const SCRIPT_TAG_REGEX = '/<script src="(.*)"/';

  // Matches URLs, even those that are protocol-relative (ie. "//" without http(s))
  const MISSING_SCHEME_REGEX = '/^(http(s)?:)?\/\/(.*)/';

  public function beforeSave()
  {
    $label = $this->getData('field_config/label');

    if(preg_match(self::SCRIPT_TAG_REGEX, $this->getValue(), $matches)) {
      $this->setValue($matches[1]);
    }

    // FILTER_VALIDATE_URL requires a protocol, so we'll have to prepend one if it's not there
    // Note: This also is the case for protocol-relative URLs, like "//foo.bar.com"
    $testVal = $this->getValue();
    if(preg_match(self::MISSING_SCHEME_REGEX, $testVal, $matches)) {
      // Protocol found, but it could be relative, so to pass the FILTER_VALIDATE_URL we need to prefix
      // with http. However, storing it as relative is fine for our purposes.
      $testVal = 'http://' . $matches[3];
    } else {
      // No protocol found, so prefix prior to testing
      $testVal = 'http://' . $testVal;

      // We need to store it with a protocol prefix as well
      $this->setValue('//' . $this->getValue());
    }

    if(!filter_var($testVal, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED | FILTER_FLAG_PATH_REQUIRED)) {
      throw new \Magento\Framework\Exception\ValidatorException(__($label . ' must either be a &lt;script&gt; tag for the Launch JavaScript snippet, or the URL to the snippet.'));
    }

    parent::beforeSave();
  }
}