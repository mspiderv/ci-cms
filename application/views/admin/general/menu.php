<div id="main-menu-wrap">

    <ul class="box" id="main-menu" data-page="menu">

        <li id="sortable-id-main-menu-item-1">

            <div class="link">

                <a href="<?= admin_url() ?>" class="text"><?= ll('admin_menu_dashboard') ?></a>

            </div>

            <div class="hover">
                
                <div class="col">

                    <h3 class="heading">
                        <a href="<?= admin_url('settings') ?>" class="text"><?= ll('admin_menu_settings') ?></a>
                    </h3>
                    
                    <div class="links">
                        
                        <p>
                            <a href="<?= admin_url('languages/add') ?>" class="plus"></a>
                            <a href="<?= admin_url('languages') ?>" class="text"><?= ll('admin_menu_languages') ?></a>
                        </p>
                        
                        <p>
                            <a href="<?= admin_url('admin_users/add') ?>" class="plus"></a>
                            <a href="<?= admin_url('admin_users') ?>" class="text"><?= ll('admin_menu_admin_users') ?></a>
                        </p>
                        
                        <p>
                            <a href="<?= admin_url('themes/add') ?>" class="plus"></a>
                            <a href="<?= admin_url('themes') ?>" class="text"><?= ll('admin_menu_themes') ?></a>
                        </p>
                        
                        <p>
                            <a href="<?= admin_url('domains/add') ?>" class="plus"></a>
                            <a href="<?= admin_url('domains') ?>" class="text"><?= ll('admin_menu_domains') ?></a>
                        </p>
                        
                        <p>
                            <a href="<?= admin_url('resources/add') ?>" class="plus"></a>
                            <a href="<?= admin_url('resources') ?>" class="text"><?= ll('admin_menu_resources') ?></a>
                        </p>

                        <p>
                            <a href="<?= admin_url('database_backups/backup') ?>" class="plus"></a>
                            <a href="<?= admin_url('database_backups') ?>" class="text"><?= ll('admin_menu_database_backups') ?></a>
                        </p>

                        <p>
                            <a href="<?= admin_url('href_attributes/add') ?>" class="plus"></a>
                            <a href="<?= admin_url('href_attributes') ?>" class="text"><?= ll('admin_menu_href_attributes') ?></a>
                        </p>

                        <p>
                            <a href="<?= admin_url('services/add') ?>" class="plus"></a>
                            <a href="<?= admin_url('services') ?>" class="text"><?= ll('admin_menu_services') ?></a>
                        </p>
                        
                        <p>
                            <a href="<?= admin_url('banned_ips/add') ?>" class="plus"></a>
                            <a href="<?= admin_url('banned_ips') ?>" class="text"><?= ll('admin_menu_banned_ips') ?></a>
                        </p>

                        <p>
                            <a href="<?= admin_url('redirects/add') ?>" class="plus"></a>
                            <a href="<?= admin_url('redirects') ?>" class="text"><?= ll('admin_menu_redirects') ?></a>
                        </p>

                        <p>
                            <a href="<?= admin_url('parts/add') ?>" class="plus"></a>
                            <a href="<?= admin_url('parts') ?>" class="text"><?= ll('admin_menu_parts') ?></a>
                        </p>

                        <p>
                            <a href="<?= admin_url('labels/add') ?>" class="plus"></a>
                            <a href="<?= admin_url('labels') ?>" class="text"><?= ll('admin_menu_labels') ?></a>
                        </p>

                    </div>

                </div>
                
                <div class="col">

                    <h3 class="heading">
                        <a href="<?= admin_url('file_manager/show') ?>" class="text"><?= ll('admin_menu_file_manager') ?></a>
                    </h3>

                    <h3 class="heading">
                        <a href="<?= admin_url('updates') ?>" class="text"><?= ll('admin_menu_updates') ?></a>
                    </h3>
                    
                    <h3 class="heading no-link">
                        <span class="text"><?= ll('admin_menu_emails_title') ?></span>
                    </h3>
                    
                    <div class="links">

                        <p>
                            <a href="<?= admin_url('email_wraps/add') ?>" class="plus"></a>
                            <a href="<?= admin_url('email_wraps') ?>" class="text"><?= ll('admin_menu_email_wraps') ?></a>
                        </p>
                        <p>
                            <a href="<?= admin_url('emails/add') ?>" class="plus"></a>
                            <a href="<?= admin_url('emails') ?>" class="text"><?= ll('admin_menu_emails') ?></a>
                        </p>
                        <p>
                            <a href="<?= admin_url('email_copies/add') ?>" class="plus"></a>
                            <a href="<?= admin_url('email_copies') ?>" class="text"><?= ll('admin_menu_email_copies') ?></a>
                        </p>

                    </div>

                    <h3 class="heading no-link">
                        <span class="text"><?= ll('admin_menu_user_guides') ?></span>
                    </h3>

                    <div class="links">

                        <p><a href="<?= site_url('user_guide/codeigniter') ?>" class="text"><?= ll('admin_menu_codeigniter') ?></a></p>
                        <p><a href="<?= site_url('user_guide/cms') ?>" class="text"><?= ll('admin_menu_cms') ?></a></p>

                    </div>

                </div>

                <div class="clear"></div>

            </div>

        </li>
        
        <?php if(cfg('general', 'eshop')): ?>
        
        <li id="sortable-id-main-menu-item-2">

            <div class="link no-link">

                <span class="text"><?= ll('admin_menu_eshop') ?></span>

            </div>

            <div class="hover">

                <div class="col">
                    
                    <h3 class="heading">
                        <a href="<?= admin_url('eshop/products/add') ?>" class="plus"></a>
                        <a href="<?= admin_url('eshop/products') ?>" class="text"><?= ll('admin_menu_products') ?></a>
                    </h3>
                    
                    <div class="links">
                        
                            <p><a href="<?= admin_url('eshop/product_types') ?>" class="text"><?= ll('admin_menu_product_types') ?></a></p>
                            <p><a href="<?= admin_url('eshop/product_parameters') ?>" class="text"><?= ll('admin_menu_product_parameters') ?></a></p>
                            <p><a href="<?= admin_url('eshop/variants') ?>" class="text"><?= ll('admin_menu_variants') ?></a></p>
                            <p><a href="<?= admin_url('eshop/product_galleries') ?>" class="text"><?= ll('admin_menu_product_galleries') ?></a></p>
                            <p><a href="<?= admin_url('eshop/manufacturers') ?>" class="text"><?= ll('admin_menu_manufacturers') ?></a></p>
                            <p><a href="<?= admin_url('eshop/distributors') ?>" class="text"><?= ll('admin_menu_distributors') ?></a></p>

                    </div>

                </div>

                <div class="col">
                    
                    <h3 class="heading">
                        <a href="<?= admin_url('eshop/categories/add') ?>" class="plus"></a>
                        <a href="<?= admin_url('eshop/categories') ?>" class="text"><?= ll('admin_menu_eshop_categories') ?></a>
                    </h3>
                    
                    <div class="links">
                        
                            <p><a href="<?= admin_url('eshop/products_in_categories') ?>" class="text"><?= ll('admin_menu_products_in_categories') ?></a></p>

                    </div>
                    
                    <h3 class="heading">
                        <a href="<?= admin_url('eshop/orders/add') ?>" class="plus"></a>
                        <a href="<?= admin_url('eshop/orders') ?>" class="text"><?= ll('admin_menu_orders') ?></a>
                    </h3>
                    
                    <div class="links">
                        
                            <p><a href="<?= admin_url('eshop/order_states') ?>" class="text"><?= ll('admin_menu_order_states') ?></a></p>
                            <p><a href="<?= admin_url('eshop/communications') ?>" class="text"><?= ll('admin_menu_communications') ?></a></p>
                            <p><a href="<?= admin_url('eshop/transport_payment') ?>" class="text"><?= ll('admin_menu_transport_payment') ?></a></p>

                    </div>

                </div>

                <div class="col">
                    
                    <h3 class="heading">
                        <a href="<?= admin_url('eshop/customers/add') ?>" class="plus"></a>
                        <a href="<?= admin_url('eshop/customers') ?>" class="text"><?= ll('admin_menu_customers') ?></a>
                    </h3>
                    
                    <div class="links">
                        
                            <p><a href="<?= admin_url('eshop/customer_groups') ?>" class="text"><?= ll('admin_menu_customer_groups') ?></a></p>

                    </div>
                    
                    <h3 class="heading no-link">
                        <span class="text"><?= ll('admin_menu_other') ?></span>
                    </h3>
                    
                    <div class="links">
                        
                            <p><a href="<?= admin_url('eshop/signs') ?>" class="text"><?= ll('admin_menu_signs') ?></a></p>
                            <p><a href="<?= admin_url('eshop/taxes') ?>" class="text"><?= ll('admin_menu_taxes') ?></a></p>
                            <p><a href="<?= admin_url('eshop/currencies') ?>" class="text"><?= ll('admin_menu_currencies') ?></a></p>
                            <p><a href="<?= admin_url('eshop/coupons') ?>" class="text"><?= ll('admin_menu_coupons') ?></a></p>

                    </div>

                </div>
                
                <div class="clear"></div>

            </div>

        </li>

        <?php endif ?>
        
        <li id="sortable-id-main-menu-item-3">

            <div class="link">

                <a href="<?= admin_url('pages/add') ?>" class="plus"></a>
                <a href="<?= admin_url('pages') ?>" class="text"><?= ll('admin_menu_pages') ?></a>

            </div>

            <div class="hover">

                <div class="col">
                    
                    <h3 class="heading">
                        <a href="<?= admin_url('pages/add') ?>" class="plus"></a>
                        <a href="<?= admin_url('pages') ?>" class="text"><?= ll('admin_menu_pages') ?></a>
                    </h3>
                    
                    <div class="links">
                        
                        <?php foreach($pages as $page_id => $page_name): ?>
                        
                            <p><a href="<?= admin_url('pages/edit/' . $page_id) ?>" class="text"><?= $page_name ?></a></p>

                        <?php endforeach ?>
                            
                    </div>

                </div>
                
                <div class="col">

                    <h3 class="heading">
                        <a href="<?= admin_url('page_types/add') ?>" class="plus"></a>
                        <a href="<?= admin_url('page_types') ?>" class="text"><?= ll('admin_menu_page_types') ?></a>
                    </h3>
                    
                    <div class="links">
                        
                        <?php foreach($page_types as $page_type_id => $page_type_name): ?>
                        
                            <p>
                                <a href="<?= admin_url('pages/add/' . $page_type_id) ?>" class="plus"></a>
                                <a href="<?= admin_url('page_types/edit/' . $page_type_id) ?>" class="text"><?= $page_type_name ?></a>
                            </p>

                        <?php endforeach ?>

                    </div>

                </div>
                
                <div class="col">
                    
                    <h3 class="heading">
                        <a href="<?= admin_url('pages_in_categories') ?>" class="text"><?= ll('admin_menu_pages_in_categories') ?></a>
                    </h3>

                    <h3 class="heading">
                        <a href="<?= admin_url('categories/add') ?>" class="plus"></a>
                        <a href="<?= admin_url('categories') ?>" class="text"><?= ll('admin_menu_page_categories') ?></a>
                    </h3>
                    
                    <div class="links">
                        
                        <?php foreach($categories as $category_id => $category_name): ?>
                        
                            <p><a href="<?= admin_url('categories/edit/' . $category_id) ?>" class="text"><?= $category_name ?></a></p>

                        <?php endforeach ?>

                    </div>

                </div>

                <div class="clear"></div>

            </div>

        </li>
        
        <li id="sortable-id-main-menu-item-4">
            
            <div class="link">

                <a href="<?= admin_url('menus/add_menu') ?>" class="plus"></a>
                <a href="<?= admin_url('menus') ?>" class="text"><?= ll('admin_menu_menus') ?></a>

            </div>

            <div class="hover">

                <div class="col">

                    <div class="line"></div>

                    <div class="links">
                        
                        <?php foreach($menus as $menu_id => $menu_name): ?>
                        
                            <p>
                                <a href="<?= admin_url('menus/add_link/' . $menu_id) ?>" class="plus"></a>
                                <a href="<?= admin_url('menus/edit_menu/' . $menu_id) ?>" class="text"><?= $menu_name ?></a>
                            </p>

                        <?php endforeach ?>

                    </div>

                </div>

                <div class="clear"></div>

            </div>

        </li>
        
        <li id="sortable-id-main-menu-item-5">

            <div class="link">

                <a href="<?= admin_url('panels/add') ?>" class="plus"></a>
                <a href="<?= admin_url('panels') ?>" class="text"><?= ll('admin_menu_panels') ?></a>

            </div>

            <div class="hover">

                <div class="col">
                    
                    <h3 class="heading">
                        <a href="<?= admin_url('panels/add') ?>" class="plus"></a>
                        <a href="<?= admin_url('panels') ?>" class="text"><?= ll('admin_menu_panels') ?></a>
                    </h3>
                    
                    <div class="links">
                        
                        <?php foreach($panels as $panel_id => $panel_name): ?>
                        
                            <p><a href="<?= admin_url('panels/edit/' . $panel_id) ?>" class="text"><?= $panel_name ?></a></p>

                        <?php endforeach ?>

                    </div>

                </div>
                
                <div class="col">

                    <h3 class="heading">
                        <a href="<?= admin_url('panel_types/add') ?>" class="plus"></a>
                        <a href="<?= admin_url('panel_types') ?>" class="text"><?= ll('admin_menu_panel_types') ?></a>
                    </h3>
                    
                    <div class="links">
                        
                        <?php foreach($panel_types as $panel_type_id => $panel_type_name): ?>
                        
                            <p>
                                <a href="<?= admin_url('panels/add/' . $panel_type_id) ?>" class="plus"></a>
                                <a href="<?= admin_url('panel_types/edit/' . $panel_type_id) ?>" class="text"><?= $panel_type_name ?></a>
                            </p>

                        <?php endforeach ?>

                    </div>

                </div>
                
                <div class="col">
                    
                    <h3 class="heading">
                        <a href="<?= admin_url('panels_in_positions') ?>" class="text"><?= ll('admin_menu_panels_in_positions') ?></a>
                    </h3>

                    <h3 class="heading">
                        <a href="<?= admin_url('positions/add') ?>" class="plus"></a>
                        <a href="<?= admin_url('positions') ?>" class="text"><?= ll('admin_menu_positions') ?></a>
                    </h3>
                    
                    <div class="links">
                        
                        <?php foreach($positions as $position_id => $position_name): ?>
                        
                            <p><a href="<?= admin_url('positions/edit/' . $position_id) ?>" class="text"><?= $position_name ?></a></p>

                        <?php endforeach ?>

                    </div>

                </div>

                <div class="clear"></div>

            </div>

        </li>

        <li id="sortable-id-main-menu-item-6">

            <div class="link">

                <a href="<?= admin_url('lists/add') ?>" class="plus"></a>
                <a href="<?= admin_url('lists') ?>" class="text"><?= ll('admin_menu_lists') ?></a>

            </div>

            <div class="hover">

                <div class="col">
                    
                    <h3 class="heading">
                        <a href="<?= admin_url('lists/add') ?>" class="plus"></a>
                        <a href="<?= admin_url('lists') ?>" class="text"><?= ll('admin_menu_lists') ?></a>
                    </h3>
                    
                    <div class="links">
                        
                        <?php foreach($lists as $list_id => $list_name): ?>
                        
                            <p>
                                <a href="<?= admin_url('lists/add_item/' . $list_id) ?>" class="plus"></a>
                                <a href="<?= admin_url('lists/edit/' . $list_id) ?>" class="text"><?= $list_name ?></a>
                            </p>

                        <?php endforeach ?>

                    </div>

                </div>
                
                <div class="col">

                    <h3 class="heading">
                        <a href="<?= admin_url('list_types/add') ?>" class="plus"></a>
                        <a href="<?= admin_url('list_types') ?>" class="text"><?= ll('admin_menu_list_types') ?></a>
                    </h3>
                    
                    <div class="links">
                        
                        <?php foreach($list_types as $list_type_id => $list_type_name): ?>
                        
                            <p>
                                <a href="<?= admin_url('lists/add/' . $list_type_id) ?>" class="plus"></a>
                                <a href="<?= admin_url('list_types/edit/' . $list_type_id) ?>" class="text"><?= $list_type_name ?></a>
                            </p>

                        <?php endforeach ?>

                    </div>

                </div>
                
                <div class="clear"></div>

            </div>

        </li>

        <li id="menu_li_info">

            <a href="<?= admin_url('login/logout') ?>" class="logged_in"><?= ll('admin_general_logged') ?></a> <?= ll('admin_general_as') ?> <strong><?= admin_user_name() ?></strong> | <a href="<?= admin_url('account') ?>" class="switch"><?= ll('admin_general_account') ?></a> | 
            <span class="lang">
                <?= ll('admin_general_language') ?> 
                <span class="langs">
                    <strong><?= strtoupper(lang()) ?></strong>
                    <?php if(count($langs) > 0): ?>
                        <span class="drop">
                            <?php foreach($langs as $lang_id => $lang): ?>
                                <a href="<?= admin_url('main/change_lang/' . $lang_id . '?' . cfg('url', 'redirect') . '=' . uri_string()) ?>"><?= $lang ?></a>
                            <?php endforeach ?>
                        </span>
                    <?php endif ?>
                </span>
            </span>

        </li>
        
    </ul>
    
    <!--
        <div class="main-menu-left"></div>
        <div class="main-menu-right"></div>
    -->

</div>