# Exceptable plugin for CakePHP #

`Exceptable any fields form conditions' plugin for CakePHP.

## Background ##

This plugin can specify fields you want exclude from conditions.

## Installation ##

1. Download this: http://github.com/k1LoW/exceptable/zipball/master
2. Unzip that download.
3. Copy the resulting folder to app/plugins
4. Rename the folder you just copied to exceptable
5. Add the following code in app_model.php

    var $actsAs = array('Exceptable.'Exceptable');

## Usage ##

    $this->Post->find('first', array('except' => 'Post.modified'));

## License ##

under MIT Lisence
