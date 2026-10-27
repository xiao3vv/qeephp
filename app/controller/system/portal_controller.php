<?php

class Controller_System_Portal extends Controller_System_Abstract
{
    function actionIndex()
    {
        $objs = Soul::find()->order('rand()')->getOne();

        $this->_view['vars'] = $objs->toArray();
    }

    function actionTest()
    {

    }

}
