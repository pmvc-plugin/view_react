[![Latest Stable Version](https://poser.pugx.org/pmvc-plugin/view_react/v/stable)](https://packagist.org/packages/pmvc-plugin/view_react)
[![Latest Unstable Version](https://poser.pugx.org/pmvc-plugin/view_react/v/unstable)](https://packagist.org/packages/pmvc-plugin/view_react)
[![CircleCI](https://circleci.com/gh/pmvc-plugin/view_react/tree/master.svg?style=svg)](https://circleci.com/gh/pmvc-plugin/view_react/tree/master)
[![License](https://poser.pugx.org/pmvc-plugin/view_react/license)](https://packagist.org/packages/pmvc-plugin/view_react)
[![Total Downloads](https://poser.pugx.org/pmvc-plugin/view_react/downloads)](https://packagist.org/packages/pmvc-plugin/view_react)

# [PMVC] PHP view template with react.js

## Install with Composer

<details><summary>CLICK TO SEE</summary><p>

### 1. Download composer

- mkdir test_folder
- curl -sS https://getcomposer.org/installer | php

### 2. Install Use composer.json or use command-line directly

#### 2.1 Install Use composer.json

- vim composer.json

```
{
    "require": {
        "pmvc-plugin/view_react": "dev-master"
    }
}
```

- php composer.phar install

#### 2.2 Or use composer command-line

- php composer.phar require pmvc-plugin/view_react

or

- composer require pmvc-plugin/view_react

</p></details>

## For debug

```
echo '{"themePath":"home"}' | node ./server.js
```

## Facebook crawler issue

- https://github.com/pmvc-plugin/view_react/issues/1
- Can't set header for none encoding, original proposal was for disable proxy cache to improve first bytes.

```
    'Content-Encoding: none'
```
