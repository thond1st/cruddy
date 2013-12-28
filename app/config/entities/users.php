<?php return array(

    'primary_column' => 'full_name',

    'form' => array(
        'model' => 'User',
        'rules' => array(
            'email' => 'required|email|max:255',
            'password' => 'required_without:id',
            'first_name' => 'max:255',
            'last_name' => 'max:255',
        ),

        'messages' => array(
            'password.required_without' => 'Необходимо указать пароль при создании пользователя.',
        ),
    ),

    'columns' => array(
        'id',

        'full_name' => array(
            'type' => 'computed',

            'value' => function ($model) {
                return $model->last_name.' '.$model->first_name;
            },

            'order_clause' => DB::raw('concat(last_name, first_name)'),

            'filter' => function ($builder, $data) {
                $builder->where($this->order_clause, 'like', "%{$data}%");
            },
        ),

        'email',
        'activated',
        'last_login' => array('order_dir' => 'desc'),
        'updated_at' => array('order_dir' => 'desc'),
    ),

    'fields' => array(
        'last_name', 'first_name',

        'email' => array(
            'type' => 'email',
            'required' => true,
        ),

        'activated' => 'bool',

        'password' => 'password',

        'groups' => 'relation',

        'permissions_string' => 'text',

        'last_login' => 'datetime',
    ),

    'related' => array('throttle'),

);