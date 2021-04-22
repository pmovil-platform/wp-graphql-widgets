<?php

use WPGraphQL\AppContext;
use WPGraphQLWidgets\Registry;
use WPGraphQLWidgets\Data\Connection\WidgetConnectionResolver;
use WPGraphQLWidgets\Data\Loader\WidgetLoader;
use WPGraphQLWidgets\Connection\WPNavMenuWidget;
use WPGraphQLWidgets\Connection\WPWidgetRecentComments;

class WPGraphQLWidgets
{
    public static function init()
    {
        add_action('register_sidebar', [__CLASS__, 'register']);
        add_action('graphql_register_types', [__CLASS__, 'registerFields']);
        add_filter('graphql_data_loaders', [__CLASS__, 'registerLoader'], 10, 2);
    }

    public static function register()
    {
        Registry::init();
    }

    public static function registerLoader( $loaders, AppContext $context )
    {
        $loaders['widget'] = new WidgetLoader($context);
        return $loaders;
    }

    public static function registerFields()
    {
        register_graphql_field(
            'RootQuery', 'widget', [
            'type' => 'WidgetInterface',
            'description' => __('Example field added to the RootQuery Type', 'replace-with-your-textdomain'),
            'resolve' => function ( $root, $args, $context, $info ) {
                return [
                    'id' => 'test',
                ];
            }
             ] 
        );

        register_graphql_connection(
            [
            'fromType' => 'RootQuery',
            'toType' => 'WidgetInterface',
            'fromFieldName' => 'widgets',
            'connectionTypeName' => 'RootQueryToWidgetConnection',
            'resolve' => function ( $root, $args, $context, $info ) {
                $resolver = new WidgetConnectionResolver($root, $args, $context, $info);
                return $resolver->get_connection();
            }
            ]
        );

        WPNavMenuWidget::register();
        WPWidgetRecentComments::register();
    }
}
