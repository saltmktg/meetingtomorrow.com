<form name="solvease_capability_form" method="POST" id="solvease_capability_form">
    <input type="hidden" name="solvease_role_cap_action" value="save_cap_changes" />
    <?php wp_nonce_field('solvease_save_capability', 'solvease_verify_capability_nonce'); ?>
    <table class="widefat solvease-rnc-table-head" cellspacing="0">
        <thead>
            <tr>
                <th id="toolbox" colspan="<?php print $this->roles_count + 1; ?>" id="columnname" class="manage-column column-columnname solvease-rnc-wide-head" scope="col">
                 <?php if(!empty($message)) { ?>
                    <div class="alert alert-<?php print $message['type']; ?>">
                        <a href="#" class="close" data-dismiss="alert">&times;</a>
                    <?php print $message['message']; ?>
                </div>
                 <?php } ?>   
                    
            <div class="clearfix solvease-rnc-role-list">
        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-2">
                <div class="form-group">
                   <input type="text" class="form-control" id="filter-capability" placeholder="<?php _e('Filter Capability', $this->translation_domain); ?>">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-8">
                <ul class="solvease-rnc-actions-list">
                    
                    <?php if (current_user_can($this->plugin_caps['add_new_role'])) { ?>
                    <li><a data-target="#solvease-add-role" data-toggle="modal" href="#"><span><i class="fa fa-plus"></i></span><?php _e('Add new role', $this->translation_domain); ?></a></li>
                    <?php } ?>
                    
                     <?php if (current_user_can($this->plugin_caps['delete_role'])) { ?>
                        <li><a data-target="#solvease_un_used_role_delete" data-toggle="modal" href="#"><span><i class="fa fa-trash"></i></span> <?php _e('Delete Role', $this->translation_domain); ?></a></li>
                     <?php } ?>
                    <?php if (current_user_can($this->plugin_caps['change_default_role'])) { ?>    
                    <li><a data-target="#solvease-change-default-role" data-toggle="modal" href="#"><span><i class="fa fa-pencil-square"></i></span><?php _e('Change Default Role', $this->translation_domain); ?> </a></li>
                    <?php } ?>
                    
                    <?php if (current_user_can($this->plugin_caps['add_new_capability'])) { ?>    
                    <li><a data-target="#solvease-add-capability" data-toggle="modal" href="#"><span><i class="fa fa-plus-square"></i></span> <?php _e('Add Capability', $this->translation_domain); ?></a></li>
                    <?php } ?>
                    
                    <?php if (current_user_can($this->plugin_caps['remove_capability'])) { ?>   
                    <li><a data-target="#solvease_rc_delete_cap" data-toggle="modal" href="#"><span><i class="fa fa-remove"></i></span><?php _e('Remove Capability', $this->translation_domain); ?> </a></li>
                    <?php } ?>
                    <!--
                    <li><a href="#"><span><i class="fa fa-edit"></i></span> Rename Capability</a></li>
                    <li><a href="#"><span><i class="fa fa-download"></i></span> Import</a></li>
                    <li><a href="#"><span><i class="fa fa-share-square"></i></span> Export</a></li>
                    -->
                    <li class="last"><a href="mailto:project.mahabub@gmail.com"><span><i class="fa fa-comments"></i></span> <span style="color: red;">Feedback / Bug report </span> </a></li>
                </ul>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-2">
                <div class="solvease-rnc-btn-block">
                    <input type="submit" name="submit" class="btn btn-primary" value="<?php _e('Save Changes', $this->translation_domain); ?>">
                </div>
            </div>
        </div>
    </div>
        </th>
        </tr>

        <tr class="main-table">
            <th id="columnname" class="manage-column column-columnname solvease-rnc-wide-head" scope="col">Capabilities</th>
            <?php if (!empty($this->roles)) { ?>
                <?php foreach ($this->roles as $roleid => $role) { ?>
                    <th id="columnname" class="manage-column column-columnname solvease-rnc-center-align" scope="col">
                        <strong>
                            <?php print $role['name']; ?> 
                        </strong>
                        <div class="role-options role-opertaion" role-id="<?php print $roleid; ?>">
                            <span><a title="Select All" class="select-all"><i class="fa fa-check-square-o"></i></a></span>
                            <span><a title="Unselect All" class="un-select-all"><i class="fa fa-square-o"></i></a></span>
                            <!-- <span><a title="Reverse" class="reverse"><i class="fa fa-rotate-left"></i></a></span> -->
                        </div>
                    
                    </th>
                <?php } ?>
            <?php } ?>
        </tr>
        </thead>
        <!-- </table> -->