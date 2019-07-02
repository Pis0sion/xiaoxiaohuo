<?php 
//根据 Annotation 自动生成的路由规则
Route::post("api/v1/account/withdraw",'api/v1.Accounts/withdraw')
	->middleware('token');
Route::post("api/v1/entry",'api/v1.Entry/userLogin');
Route::post("api/v1/register",'api/v1.Entry/register');
Route::post("api/v1/send/msg",'api/v1.Entry/sendMsgToOperate');
Route::post("api/v1/forget/pwd",'api/v1.Entry/forgetToEntryPassword');
Route::get("api/v1/banner",'api/v1.Home/banners');
Route::get("api/v1/goods/home",'api/v1.Home/products');
Route::get("api/v1/integral/list",'api/v1.Integral/getListByProducts');
Route::post("api/v1/pro/:goods_id/details",'api/v1.Integral/productByDetails')
	->model('goods_id','\app\common\model\IntegralMalls',false)
	->middleware('token');
Route::post("api/v1/multiple",'api/v1.Integral/getMultipleTypes');
Route::post("api/v1/prepare/:goods_id/orders",'api/v1.Integral/prepareToPlaceOrders')
	->model('goods_id','\app\common\model\IntegralMalls',false)
	->middleware('token');
Route::post("api/v1/place/orders",'api/v1.Integral/placeOrders')
	->middleware('token');
Route::get("api/v1/order/integral",'api/v1.IntegralOrders/demo');
Route::post("api/v1/submit/profits",'api/v1.Profits/addProfit');
Route::post("api/v1/profit",'api/v1.Profits/everyDaysToProfit');
Route::post("api/v1/cates",'api/v1.Shop/category');
Route::post("api/v1/pros/:gc_id/cate",'api/v1.Shop/getProductsByCategory')
	->model('gc_id','\app\common\model\GoodsCategorys',false);
Route::post("api/v1/details/product",'api/v1.Shop/getProductToDetails');
Route::post("api/v1/search/product",'api/v1.Shop/getProductByKeyWords');
Route::post("api/v1/upgrade",'api/v1.UpGrade/isCheckUpgrade');
Route::post("api/v1/users/info",'api/v1.Users/getUsersInformations')
	->middleware('token');
Route::post("api/v1/sys/link",'api/v1.Users/getLinker');
Route::post("api/v1/portrait",'api/v1.Users/uploadToUserHeadPortrait')
	->middleware('token');
Route::post("api/v1/alter/profile",'api/v1.Users/alterToUsersNickName')
	->middleware('token');
Route::post("api/v1/isreal/users",'api/v1.Users/usersToCertification')
	->middleware('token');
Route::post("api/v1/is/real",'api/v1.Users/getUsersToCertifications')
	->middleware('token');
Route::post("api/v1/bind/bank",'api/v1.Users/userToBindBankCard')
	->middleware('token');
Route::post("api/v1/banks/user",'api/v1.Users/userToBindBanks')
	->middleware('token');
Route::post("api/v1/del/:ubc_id/bank",'api/v1.Users/userToDelBanks')
	->model('ubc_id','\app\common\model\BindBankCards',false)
	->middleware('token');
Route::post("api/v1/get/default/bank",'api/v1.Users/getUsersToDefaultBanks')
	->middleware('token');
Route::post("api/v1/default/:ubc_id/bank",'api/v1.Users/userToSetDefaultBanks')
	->model('ubc_id','\app\common\model\BindBankCards',false)
	->middleware('token');
Route::post("api/v1/consigns/list",'api/v1.Users/getConsignsList')
	->middleware('token');
Route::post("api/v1/consigns/default",'api/v1.Users/getDefaultConsigns')
	->middleware('token');
Route::post("api/v1/add/consign",'api/v1.Users/addConsigns')
	->middleware('token');
Route::post("api/v1/default/:uc_id/consign",'api/v1.Users/setDefaultConsigns')
	->model('uc_id','\app\common\model\UserConsigns',false)
	->middleware('token');
Route::post("api/v1/edit/:uc_id/consign",'api/v1.Users/editUserConsigns')
	->model('uc_id','\app\common\model\UserConsigns',false)
	->middleware('token');
Route::post("api/v1/del/:uc_id/consign",'api/v1.Users/delUserConsigns')
	->model('uc_id','\app\common\model\UserConsigns',false)
	->middleware('token');
Route::post("api/v1/team",'api/v1.Users/getDirectUsers')
	->middleware('token');
Route::post("api/v1/getUserProfitLog",'api/v1.Users/getUserProfitLog')
	->middleware('token');
Route::post("api/v1/getUseruaIntegral",'api/v1.Users/getUseruaIntegral')
	->middleware('token');
Route::post("api/v1/getTeamProfits",'api/v1.Users/getTeamProfits')
	->middleware('token');
Route::post("api/v1/withdraw/list",'api/v1.Users/getWithdrawList')
	->middleware('token');
Route::post("api/v1/bank",'api/v1.Users/getUserBankLists');