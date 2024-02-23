<div class="modal fade" id="threeDots" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moreTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="moreBody" >
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="threeDots2" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moreTitle2"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="moreBody2" >
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="playVideo" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="videoPlayerTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="videoPlayer">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="settings" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row w-100">
                    <div class="col-12 p-3 text-center">
                        <?php 
                        if( empty($profileData["logo"]) ){
                            $image = "https://i.imgur.com/nUUVoNt.png";
                        }else{
                            $image = "logos/".$profileData["logo"];
                        }
                        ?>
                        <img src="<?php echo $image ?>" class="profileLogo rounded-circle" style="width:100px;height:100px">
                    </div>
                    <div id="profileOptions0" style="display:block">
                    <div class="col" data-bs-toggle="modal" data-bs-target="#login">
                        الدخول
                    </div>
                    <hr>
                    <div class="col" data-bs-toggle="modal" data-bs-target="#signup">
                        التسجيل
                    </div>
                    <hr>
                    </div>
                    <div id="profileOptions1" style="display:none">
                    <div class="col" data-bs-toggle="modal" data-bs-target="#profile">
                        الملف الشخصي
                    </div>
                    <div class="col" data-bs-toggle="modal" data-bs-target="#likedVidoesModal"  style="display:none">
                        <label id="likedVideosLabel">Liked Videos</label>
                    </div>
                    <div class="col" data-bs-toggle="modal" data-bs-target="#disLikedVidoesModal"  style="display:none">
                    <label id="disLikedVideosLabel">Dis-Liked Videos</label>
                    </div>
                    <div class="col" data-bs-toggle="modal" data-bs-target="#history" style="display:none">
                        <label id="historyLabel">History</label>
                    </div>
                    <hr>
                    <div class="col" data-bs-toggle="modal" data-bs-target="#request">
                        إطلب
                    </div>
                    <hr>
                    <div class="col" data-bs-toggle="modal" data-bs-target="#contactUs">
                        تواصل معنا
                    </div>
                    <hr>
                    <div class="col">
                        <label id="logoutLabel">الخروج</label>
                    </div>
                    <hr>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="login" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">تسجيل الدخول</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row w-100 m-0">
                    <div class="col-12">
                        إسم المستخدم
                    </div>
                    <div class="col-12">
                        <input class="form-control" type="text" name="loginUser">
                    </div>
                    <div class="col-12 mt-2">
                        كلمة المرور
                    </div>
                    <div class="col-12">
                        <input class="form-control" type="password" name="loginPass">
                    </div>
                    <div class="col-12 mt-3">
                        <div class="row w-100 m-0 p-0">
                            <div class="col-6"><div class="btn btn-primary w-100" id="loginBtn">دخول</div></div>
                            <div class="col-6"><div data-bs-toggle="modal" data-bs-target="#forget" class="btn btn-secondary w-100">نسيان</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="forget" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">نسيان كلمة المرور</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row w-100 m-0">
                    <div class="col-12">
                        البريد الإلكتروني
                    </div>
                    <div class="col-12">
                        <input class="form-control" type="email" name="forgetEmail">
                    </div>
                    <div class="col-12 mt-3">
                        <div class="row w-100 m-0 p-0">
                            <div class="col-12"><div class="btn btn-primary w-100" id="forgetBtn">أرسل كلمة مرور جديده</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="signup" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">التسجيل</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row w-100 m-0">
                    <div class="col-12">
                        إسم المستخدم
                    </div>
                    <div class="col-12">
                        <input class="form-control" type="text" name="regUser">
                    </div>
                    <div class="col-12">
                        البريد الإلكتروني
                    </div>
                    <div class="col-12">
                        <input class="form-control" type="email" name="regEmail">
                    </div>
                    <div class="col-12 mt-2">
                        كلمة المرور
                    </div>
                    <div class="col-12">
                        <input class="form-control" type="password" name="regPass">
                    </div>
                    <div class="col-12 mt-2">
                        إعادة كلمة المرور
                    </div>
                    <div class="col-12">
                        <input class="form-control" type="password" name="regPass1">
                    </div>
                    <div class="col-12 mt-3">
                        <div class="row w-100 m-0 p-0">
                            <div class="col-6"><div class="btn btn-primary w-100" id="regBtn">التسجيل</div></div>
                            <div class="col-6"><div class="btn btn-secondary w-100" data-bs-toggle="modal" data-bs-target="#login">الدخول</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade profileLable" id="profile" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileLable">الملف الشخصي</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row w-100 m-0">
                    <div class="col-12">
                        إسم المستخدم
                    </div>
                    <div class="col-12">
                        <input class="form-control" type="text" name="profileUser" value="<?php echo $profileData["username"] ?>" disabled>
                    </div>
                    <div class="col-12">
                        البريد الإلكتروني
                    </div>
                    <div class="col-12">
                        <input class="form-control" type="email" name="profileEmail" value="<?php echo $profileData["email"] ?>" disabled>
                    </div>
                    <div class="col-12 mt-2">
                        كلمة المرور
                    </div>
                    <div class="col-12">
                        <input class="form-control" type="password" name="profilePass">
                    </div>
                    <div class="col-12 mt-2">
                        إعادة كلمة المرور
                    </div>
                    <div class="col-12">
                        <input class="form-control" type="password" name="profilePass1">
                    </div>
                    <div class="col-12">
                        UptoBox Token
                    </div>
                    <div class="col-12">
                        <input class="form-control" type="text" name="profileToken" value="<?php echo $profileData["uptobox"] ?>">
                    </div>
                    <div class="col-12">
                        الصورة الشخصية
                    </div>
                    <div class="col-12">
                        <input class="form-control" type="file" name="profileImage" id="profileLogo">
                    </div>
                    <div class="col-12 mt-3">
                        <div class="row w-100 m-0 p-0">
                            <div class="col-12"><div class="btn btn-primary w-100" id="profileBtn">أرسل</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="contactUs" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">تواصل معنا</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row w-100 m-0">
                    <div class="col-12">
                        العنوان
                    </div>
                    <div class="col-12">
                        <input class="form-control" type="text" name="conTitle">
                    </div>
                    <div class="col-12">
                        الرسالة
                    </div>
                    <div class="col-12">
                        <textarea class="form-control" name="conMsg"></textarea>
                    </div>
                    <div class="col-12 mt-3">
                        <div class="row w-100 m-0 p-0">
                            <div class="col-12"><div class="btn btn-primary w-100" id="conBtn">أرسل</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="request" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">الطلب</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row w-100 m-0">
                    <div class="col-12">
                        العنوان
                    </div>
                    <div class="col-12">
                        <input class="form-control" type="text" name="reqTitle">
                    </div>
                    <div class="col-12">
                        رابط للصورة او لصفحة الفلم
                    </div>
                    <div class="col-12">
                        <input class="form-control" type="text" name="reqIMDb">
                    </div>
                    <div class="col-12">
                        معلومات اخرى
                    </div>
                    <div class="col-12">
                        <textarea class="form-control" name="reqMsg"></textarea>
                    </div>
                    <div class="col-12 mt-3">
                        <div class="row w-100 m-0 p-0">
                            <div class="col-12"><div class="btn btn-primary w-100" id="reqBtn">أرسل</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="likedVidoesModal" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">Liked Vidoes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="likedVidoesBody">
                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="disLikedVidoesModal" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">Dis-liked Vidoes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="disLikedVidoesBody">
                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="history" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="historyBody">
                
            </div>
        </div>
    </div>
</div>