<?php defined('ABSPATH') || exit;

$text = sprintf
(
    '%s %s %s <hr>',
    __('Your copy of the free software has not been activated.', 'wc1c-main'),
    __('We recommend that you activate your copy of the free software for stable updates and better performance.', 'wc1c-main'),
    __('After activation, this section will disappear and will no longer be shown.', 'wc1c-main')
);
?>

<div class="row g-0">
    <div class="col-24 col-lg-17 p-0">
        <div class="pe-0 pe-lg-2">
            <div class="alert wc1c-configurations-alert mb-2 mt-2">
                <p class="fs-6"><?php echo wp_kses_post($text); ?></p>

                <div class="">
                    <h2><?php _e('How to activate?', 'wc1c-main'); ?></h2>
                    <ul>
                        <li class="fs-6"><b>1.</b> <?php _e('Get an activation code in any available way. For example, on the official website.', 'wc1c-main'); ?> (<a href="://wc1c.info/market/code">wc1c.info/market/code</a>)</li>
                        <li class="fs-6"><b>2.</b> <?php _e('Enter the activation code in the plugin settings.', 'wc1c-main'); ?> (<a href="<?php printf('%s', get_home_url('', add_query_arg(['section' => 'settings', 'do_settings' => 'activation']))) ?>"><?php printf('%s', get_home_url('', add_query_arg(['section' => 'settings', 'do_settings' => 'activation']))) ?></a>)</li>
                    </ul>
                </div>

                <div class="">
                    <h2><?php _e('Why is activation required?', 'wc1c-main'); ?></h2>
                    <p class="fs-6">
                        <?php _e('You received a copy of the software completely free of charge and you can use it as is without any activation.', 'wc1c-main'); ?>
                        <?php _e('However, in order to receive timely, as well as necessary updates and improvements, it is necessary to activate the current environment.', 'wc1c-main'); ?>
                    </p>
                    <p class="fs-6">
                        <?php _e('Activation is vital for the performance of the plugin and its further active development. Dont ignore activation.', 'wc1c-main'); ?>
                        <?php _e('Each activation triggers a mechanism to improve the software you use.', 'wc1c-main'); ?>
                    </p>
                    <p class="fs-6">
                        <?php _e('In addition to supporting the software you use, additional features will be added.', 'wc1c-main'); ?>
                    </p>
                </div>

            </div>

        </div>
    </div>
    <div class="col-24 col-lg-7 p-0">

        <div class="alert alert-info border-0 mt-2" style="max-width: 100%;">
            <h4 class="alert-heading mt-0 mb-1"><?php _e('Do not wait until something breaks!', 'wc1c-main'); ?></h4>
            <?php _e('Activate your current copy of the software.', 'wc1c-main'); ?>
            <hr>
            <?php _e('Buy code:', 'wc1c-main'); ?> <a target="_blank" href="//wc1c.info/market/code">wc1c.info/market/code</a>
        </div>

        <div class="alert alert-secondary border-0 mt-2" style="max-width: 100%;">
            <h4 class="alert-heading mt-0 mb-1"><?php _e('No financial opportunity?', 'wc1c-main'); ?></h4>
            <?php _e('Take part in the development of the solution you use.', 'wc1c-main'); ?>
            <br/>
            <?php _e('Information on how to participate is available in the official documentation on the official website.', 'wc1c-main'); ?>
            <hr>
            <?php _e('Docs:', 'wc1c-main'); ?> <a target="_blank" href="//wc1c.info/docs">wc1c.info/docs</a>
        </div>

        <div class="alert alert-secondary border-0 mt-2" style="max-width: 100%;">
            <h4 class="alert-heading mt-0 mb-1"><?php _e('Every activation counts!', 'wc1c-main'); ?></h4>
            <?php _e('By activating your project, you let the WC1C team know that the plugin is in active use.', 'wc1c-main'); ?>
            <br/>
            <?php _e('Also, you give a financial opportunity to release compatibility updates and add new features!', 'wc1c-main'); ?>
        </div>
    </div>
</div>