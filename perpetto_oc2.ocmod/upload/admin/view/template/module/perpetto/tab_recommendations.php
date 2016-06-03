<div id="dashboard">
    <div class="perpetto-body">
        <div class="row">
            <div class="col-xs-12 col-sm-4">
                <div class="box">
                    <h3>Dashboard</h3>
                    <span>View your Dashboard to see how Perpetto is helping your store.</span>
                    <a target="_blank" href='<?php if(!empty($account_info)) echo "https://admin.perpetto.com/#/account/".$account_info->data->store->account_id."/dashboard/".$account_info->data->store->id."/recommendations/"; else echo ""; ?>' class="btn btn-default">View Your Perpetto Dashboard</a>
                </div>
            </div>
            <div class="col-xs-12 col-sm-4">
                <div class="box">
                    <h3>Ask-Us-Anything Support</h3>
                    <span>Ask us anything you might need to know. We are here to help.</span>
                    <a target="_blank" href='<?php if(!empty($account_info)) echo "https://admin.perpetto.com/#/account/".$account_info->data->store->account_id."/store/details/".$account_info->data->store->id."/settings/intercom"; else echo ""; ?>' class="btn btn-default">Get In Touch</a>
                </div>
            </div>
            <div class="col-xs-12 col-sm-4">
                <div class="box">
                    <?php if(!empty($card_added) && !$card_added) { ?>
                    <h3>Free Trial (<?php echo $trial_days_left ?> Days Left)</h3>
                    <span>Don't forget to add your billing information before the trial ends.</span>
                    <a target="_blank" href='<?php if(!empty($account_info)) echo "https://admin.perpetto.com/#/account/".$account_info->data->store->account_id."/settings/billing/"; else echo ""; ?>' class="btn btn-default">Add Billing Information Securely</a>
                    <?php } else { ?>
                        <h3>Billing information</h3>
                        <span>You can access your billing information from here.</span>
                        <a target="_blank" href='<?php if(!empty($account_info)) echo "https://admin.perpetto.com/#/account/".$account_info->data->store->account_id."/settings/billing/"; else echo ""; ?>' class="btn btn-default">Access Your Billing Information</a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <div class="recommendation-slots">        
        <?php if(!empty($slots)) { ?>
        <div class="row">
            <div class="col-xs-6 col-sm-7 col-md-7 col-lg-9">
                <h3>Recommendation Slots</h3>
                <span>Here you can see what recommendations are installed. You can see how they will look with the "Preview" button and change their algorithm, number of displayed items or styling in the Perpetto administration area using the "Edit Recommendations" button.</span>
            </div>
            <div class="col-xs-6 col-sm-5 col-md-5 col-lg-3">
                <div class="actions">
                    <a target="_blank" href="<?php echo $live_preview_link ?>" class="btn btn-primary mr5">Preview</a>
                    <a target="_blank" href='<?php if(!empty($account_info)) echo "https://admin.perpetto.com/#/account/".$account_info->data->store->account_id."/recs/".$account_info->data->store->id."/list"; else echo ""; ?>' class="btn btn-primary">Edit Recommendations</a>
                </div>
            </div>
        </div>
        <br />
        <div class="row">
            <div class="col-xs-12">
                <?php if(!empty($slots['home_page'])) { ?>
                <div class="page row">
                    <div class="col-sm-2 col-xs-12">
                        <h3>Homepage</h3>
                    </div>
                    <div class="col-sm-10 col-xs-12">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="22%">Slot title</th>
                                    <th width="20%">Recommendation algorithm</th>
                                    <th width="30%">Slot container</th>
                                    <th width="28%">Position</th>
                                </tr>
                            </thead>
                            <?php foreach($slots['home_page'] as $slot) { ?>
                            <tr>
                                <td>
                                   <?php echo $slot->title; ?>
                                </td>
                                <td>
                                    <?php echo ucwords(str_replace('_',' ',$slot->engine_name)); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars('<div class="ptto-rec-slot-token" data-ptto-token="' . $slot->token . '"></div>'); ?>
                                </td>
                                <td>
                                    <select class="form-control ptto-slot-position" data-ptto-token="<?php echo $slot->token ?>">
                                        <option value="content_top" <?php if(!empty($slot->position) && $slot->position == "content_top") echo "selected=selected"; ?>>Content Top</option>
                                        <option value="content_bottom" <?php if(empty($slot->position) || !empty($slot->position) && $slot->position == "content_bottom") echo "selected=selected"; ?>>Content Bottom</option>
                                        <option value="column_left" <?php if(!empty($slot->position) && $slot->position == "column_left") echo "selected=selected"; ?>>Column Left</option>
                                        <option value="column_right" <?php if(!empty($slot->position) && $slot->position == "column_right") echo "selected=selected"; ?>>Column Right</option>
                                    </select>
                                </td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
                <?php } ?>
                <?php if(!empty($slots['product_page'])) { ?>
                <div class="page row">
                    <div class="col-sm-2 col-xs-12">
                        <h3>Product Page</h3>
                    </div>
                    <div class="col-sm-10 col-xs-12">
                        <table class="table e">
                            <thead>
                                <tr>
                                    <th width="22%">Slot title</th>
                                    <th width="20%">Recommendation algorithm</th>
                                    <th width="30%">Slot container</th>
                                    <th width="28%">Position</th>
                                </tr>
                            </thead>
                            <?php foreach($slots['product_page'] as $slot) { ?>
                            <tr>
                                <td>
                                   <?php echo $slot->title; ?>
                                </td>
                                <td>
                                    <?php echo ucwords(str_replace('_',' ',$slot->engine_name)); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars('<div class="ptto-rec-slot-token" data-ptto-token="' . $slot->token . '"></div>'); ?>
                                </td>
                                <td>
                                    <select class="form-control ptto-slot-position" data-ptto-token="<?php echo $slot->token ?>">
                                        <option value="content_top" <?php if(!empty($slot->position) && $slot->position == "content_top") echo "selected=selected"; ?>>Content Top</option>
                                        <option value="content_bottom" <?php if(empty($slot->position) || !empty($slot->position) && $slot->position == "content_bottom") echo "selected=selected"; ?>>Content Bottom</option>
                                        <option value="column_left" <?php if(!empty($slot->position) && $slot->position == "column_left") echo "selected=selected"; ?>>Column Left</option>
                                        <option value="column_right" <?php if(!empty($slot->position) && $slot->position == "column_right") echo "selected=selected"; ?>>Column Right</option>
                                    </select>
                                </td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
                <?php } ?>
                <?php if(!empty($slots['category_page'])) { ?>
                <div class="page row">
                    <div class="col-sm-2 col-xs-12">
                        <h3>Category Page</h3>
                    </div>
                    <div class="col-sm-10 col-xs-12">
                        <table class="table e">
                            <thead>
                                <tr>
                                    <th width="22%">Slot title</th>
                                    <th width="20%">Recommendation algorithm</th>
                                    <th width="30%">Slot container</th>
                                    <th width="28%">Position</th>
                                </tr>
                            </thead>
                            <?php foreach($slots['category_page'] as $slot) { ?>
                            <tr>
                                <td>
                                   <?php echo $slot->title; ?>
                                </td>
                                <td>
                                    <?php echo ucwords(str_replace('_',' ',$slot->engine_name)); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars('<div class="ptto-rec-slot-token" data-ptto-token="' . $slot->token . '"></div>'); ?>
                                </td>
                                <td>
                                    <select class="form-control ptto-slot-position" data-ptto-token="<?php echo $slot->token ?>">
                                        <option value="content_top" <?php if(!empty($slot->position) && $slot->position == "content_top") echo "selected=selected"; ?>>Content Top</option>
                                        <option value="content_bottom" <?php if(empty($slot->position) || !empty($slot->position) && $slot->position == "content_bottom") echo "selected=selected"; ?>>Content Bottom</option>
                                        <option value="column_left" <?php if(!empty($slot->position) && $slot->position == "column_left") echo "selected=selected"; ?>>Column Left</option>
                                        <option value="column_right" <?php if(!empty($slot->position) && $slot->position == "column_right") echo "selected=selected"; ?>>Column Right</option>
                                    </select>
                                </td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
                <?php } ?>
                <?php if(!empty($slots['cart_page'])) { ?>
                <div class="page row">
                    <div class="col-sm-2 col-xs-12">
                        <h3>Cart Page</h3>
                    </div>
                    <div class="col-sm-10 col-xs-12">
                        <table class="table e">
                            <thead>
                                <tr>
                                    <th width="22%">Slot title</th>
                                    <th width="20%">Recommendation algorithm</th>
                                    <th width="30%">Slot container</th>
                                    <th width="28%">Position</th>
                                </tr>
                            </thead>
                            <?php foreach($slots['cart_page'] as $slot) { ?>
                            <tr>
                                <td>
                                   <?php echo $slot->title; ?>
                                </td>
                                <td>
                                    <?php echo ucwords(str_replace('_',' ',$slot->engine_name)); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars('<div class="ptto-rec-slot-token" data-ptto-token="' . $slot->token . '"></div>'); ?>
                                </td>
                                <td>
                                    <select class="form-control ptto-slot-position" class="form-control" data-ptto-token="<?php echo $slot->token ?>">
                                        <option value="content_top" <?php if(!empty($slot->position) && $slot->position == "content_top") echo "selected=selected"; ?>>Content Top</option>
                                        <option value="content_bottom" <?php if(empty($slot->position) || !empty($slot->position) && $slot->position == "content_bottom") echo "selected=selected"; ?>>Content Bottom</option>
                                        <option value="column_left" <?php if(!empty($slot->position) && $slot->position == "column_left") echo "selected=selected"; ?>>Column Left</option>
                                        <option value="column_right" <?php if(!empty($slot->position) && $slot->position == "column_right") echo "selected=selected"; ?>>Column Right</option>
                                    </select>
                                </td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
<script>
    $(document).ready(function() {
        $( ".ptto-slot-position" ).change(function() {
            var selected_position = $(this).val();
            var slot_token = $(this).attr('data-ptto-token');
          
            $.ajax({
                url: '<?php echo $change_slot_position; ?>',
                type: 'POST',
                data: {'slot_position' : selected_position, 'slot_token' : slot_token, 'store_id': '<?php echo $store["store_id"]; ?>'},
                dataType: 'json',
                success: function (response) {
                    location.reload();
                    
                }
            });
        });
    });
</script>
