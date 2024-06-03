
    <!-- 模組 submitModal-->
    <div class="modal fade" id="submitModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Do you submit this：<span id="idty_title"></span>&nbsp?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body px-5">
                    <!-- 第二排的功能 : 搜尋功能 -->
                    <div class="row unblock" id="forwarded">
                        <div class="col-12" id="searchUser_table">
                            <div class="input-group search" id="select_inSign_Form">
                                <span class="input-group-text form-label">轉呈</span>
                                <input type="text" name="in_sign" id="in_sign" class="form-control" placeholder="請輸入工號"
                                        aria-label="請輸入查詢對象工號" onchange="search_fun(this.id, this.value);">
                                <div id="in_sign_badge"></div>
                                <input type="hidden" name="in_signName" id="in_signName" class="form-control">
                            </div>
                        </div>
                        <hr>
                    </div>
                    <label for="sign_comm" id="sign_comm_label" class="form-check-label" >command：</label>
                    <textarea name="sign_comm" id="sign_comm" class="form-control" rows="5"></textarea>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="updated_user"    id="updated_user"   value="<?php echo $auth_cname;?>">
                    <input type="hidden" name="updated_emp_id"  id="updated_emp_id" value="<?php echo $auth_emp_id;?>">
                    <input type="hidden" name="uuid"            id="uuid"           value="<?php echo $receive_row['uuid'];?>">
                    <input type="hidden" name="fab_sign_code"   id="fab_sign_code"  value="<?php echo $receive_row['fab_sign_code'];?>">
                    <input type="hidden" name="action"          id="action"         value="<?php echo $action;?>">
                    <input type="hidden" name="step"            id="step"           value="<?php echo $step;?>">
                    <input type="hidden" name="idty"            id="idty"           value="">
                    <input type="hidden" name="old_idty"        id="old_idty"       value="<?php echo $receive_row['idty'];?>">
                    <?php if($sys_role <= 3){ ?>
                        <button type="submit" name="receive_submit" value="Submit" class="btn btn-primary" ><i class="fa fa-paper-plane" aria-hidden="true"></i> Agree</button>
                    <?php } ?>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>