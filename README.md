[![Latest Stable Version](https://poser.pugx.org/pmvc-plugin/view_react/v/stable)](https://packagist.org/packages/pmvc-plugin/view_react) 
[![Latest Unstable Version](https://poser.pugx.org/pmvc-plugin/view_react/v/unstable)](https://packagist.org/packages/pmvc-plugin/view_react) 
[![Build Status](https://travis-ci.org/pmvc-plugin/view_react.svg?branch=master)](https://travis-ci.org/pmvc-plugin/view_react)
[![License](https://poser.pugx.org/pmvc-plugin/view_react/license)](https://packagist.org/packages/pmvc-plugin/view_react)
[![Total Downloads](https://poser.pugx.org/pmvc-plugin/view_react/downloads)](https://packagist.org/packages/pmvc-plugin/view_react) 

# [PMVC] PHP view template with react.js  
===============

## Install with Composer
### 1. Download composer
   * mkdir test_folder
   * curl -sS https://getcomposer.org/installer | php

### 2. Install Use composer.json or use command-line directly
#### 2.1 Install Use composer.json
   * vim composer.json
```
{
    "require": {
        "pmvc-plugin/view_react": "dev-master"
    }
}
```
   * php composer.phar install

#### 2.2 Or use composer command-line
   * php composer.phar require pmvc-plugin/view_react


## For debug
```
echo '{"themePath":"home"}' | node ./server.js
```

## Facebook crawler issue
* https://github.com/pmvc-plugin/view_react/issues/1
* Can't set header for none encoding, original proposal was for disable proxy cache to improve first bytes.
```
    'Content-Encoding: none'
```
