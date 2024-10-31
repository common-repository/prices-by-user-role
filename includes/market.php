<?php
?>
<style>
    .box14{
        width: 40%;
        margin-top:2px;
        min-height: 310px;
        margin-right: 400px;
        padding:10px;
        position:absolute;
        z-index:1;
        right:0px;
        float:left;
        background: -webkit-gradient(linear, 0% 20%, 0% 92%, from(#fff), to(#f3f3f3), color-stop(.1,#fff));
        border: 1px solid #ccc;
        -webkit-border-radius: 60px 5px;
        -webkit-box-shadow: 0px 0px 35px rgba(0, 0, 0, 0.1) inset;
    }
    .box14_ribbon{
        position:absolute;
        top:0; right: 0;
        width: 130px;
        height: 40px;
        background: -webkit-gradient(linear, 555% 20%, 0% 92%, from(rgba(0, 0, 0, 0.1)), to(rgba(0, 0, 0, 0.0)), color-stop(.1,rgba(0, 0, 0, 0.2)));
        border-left: 1px dashed rgba(0, 0, 0, 0.1);
        border-right: 1px dashed rgba(0, 0, 0, 0.1);
        -webkit-box-shadow: 0px 0px 12px rgba(0, 0, 0, 0.2);
        -webkit-transform: rotate(6deg) skew(0,0) translate(-60%,-5px);
    }
    .box14 h3
    {
        text-align:center;
        margin:2px;
    }
    .box14 p
    {
        text-align:center;
        margin:2px;
        border-width:1px;
        border-style:solid;
        padding:5px;
        border-color: rgb(204, 204, 204);
    }
    .box14 span
    {
        background:#fff;
        padding:5px;
        display:block;
        box-shadow:green 0px 3px inset;
        margin-top:10px;
    }
    .box14 img {
        width: 40%;
        padding-left:30%;
        margin-top: 5px;
    }
    .table-box-main {
        box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
        transition: all 0.3s cubic-bezier(.25,.8,.25,1);
    }

    .table-box-main:hover {
        box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
    }
    span ul li{
        margin:4px;
    }
</style>
<div class="box14 table-box-main">
    <h3>
        <center><a href="https://www.xadapter.com/" target="_blank" style="text-decoration:  none;color:black;" >XAdapter</a></center></h3> 
    <hr>
    <img src="https://cdn.xadapter.com/wp-content/uploads/2016/07/10-Prices-by-User-Role-for-WooCommerce.jpg">
    <h3>WooCommerce Catalog Mode, Wholesale & Role Based Pricing</h3>
    <br/> <center><a href="http://www.xadapter.com/product/prices-by-user-role-for-woocommerce/" target="_blank" class="button button-primary">Upgrade to Premium Version</a></center>
    <span>
   <ul style="list-style-type:disc;">
      <p style="color:red;"><strong>Your Business is precious! Go Premium!</strong></p>
      <p>Basic version only supports Simple Products.</p>
      <p style="text-align:left">
         - Supports Variable & Grouped Products (Basic version only Supports Simple Products).<br>
         - Discount/Markup on regular and sale price.<br>
         - Hide Individual Products for various user roles.<br>
         - Timely compatibility updates and bug fixes.<br>
         - Premium support!<br>
      </p>
   </ul>
</span>


    <center> <a href="https://www.xadapter.com/category/documentation/prices-by-user-role-for-woocommerce/" target="_blank" class="button button-primary">Documentation</a> <a href="http://pricingbyroledemo.adaptxy.com" target="_blank" class="button button-primary">Live Demo</a></center>
</div>
<script>
    jQuery(document).ready(function () {
        eh_pricing_discount_manage_role('add_user_role');
    });
    function eh_pricing_discount_manage_role(manage_role) {
        jQuery("input[name='save']").hide();
    }
</script>