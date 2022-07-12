<?php

$default_admin_vaidations_auto = array('name' => array(
        array(
            array(
                'type' => 'presence',
                'fype' => 'select'
            )
        ),
        array(
            array(
                'type' => 'presence',
                'fype' => 'text'
            )
        ),
        array(
            array(
                'type' => 'presence',
                'fype' => 'text'
            )
        ),
        array(
            array(
                'type' => 'presence',
                'fype' => 'text'
            )
        ),
        array(
            array(
                'type' => 'presence',
                'fype' => 'text'
            )
        ),
    ),
    'date' => array(),
    'text' => array(array(
            array(
                'type' => 'presence',
                'fype' => 'text'
            )
        )),
    'textarea' => array(
        array(
            array(
                'type' => 'presence',
                'fype' => 'textarea'
            )
        )
    ),
    'radio' => array(array(
            array(
                'type' => 'presence',
                'fype' => 'radio'
            ))
    ),
    'select' => array(array(
            array(
                'type' => 'presence',
                'fype' => 'select'
            ))
    ),
    'number' => array(array(
            array(
                'type' => 'presence',
                'fype' => 'text'
            ))
    ),
    'multiselect' => array(array(
            array(
                'type' => 'presence',
                'fype' => 'text'
            )
        )),
    'checkbox' => array(array(
            array(
                'type' => 'presence',
                'fype' => 'checkbox'
            ))
    )
);
