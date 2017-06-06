<?php

class Table extends Annotation {

    public $name;

}

class Id extends Annotation {

    public $name;

}

class Column extends Annotation {

    public $name;

}

class Type extends Annotation {

    

}

class Relation extends Annotation {

    public $target;
    public $column;
    public $type;
    public $attribute;

}

class AutoGenerator extends Annotation {
    
}

class PreAction extends Annotation {
    
}

