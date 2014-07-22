<style>
    html.error {
        background-color: #F2F2F2;
        height: 100%;
    }
    html.error, body {
        height: 100%;
    }
    *, *:before, *:after {
        box-sizing: border-box;
    }
    html.error body {
        color: #424F59;
        font: message-box;
        font-size: 14px;
        height: 100%;
        margin: 0;
    }
    html.error div.box{
        position: relative;
        top:80px;
        width: 420px;
        min-height: 10px;
        margin: 0 auto;
        background: #FFF;
        padding: 60px 40px 40px;
        text-align: center;

        -webkit-box-shadow: 0 1px 3px 0 rgba(0,0,0,0.25);
        box-shadow: 0 1px 3px 0 rgba(0,0,0,0.25);
        -webkit-border-radius: 5px;
        border-radius: 5px;
    }
    html.error header h1 {
        font-size: 24px;
        font-weight: 200;
        line-height: 1em;
        margin: 0px 0px 32px;
    }
    html.error section p{
        font-size:18px;
    }
    .table_error_handler{
        width: 100%;
        padding: 0;
        margin: 0 0 10px 0;
        border-collapse:collapse;
        font-size: 12px;
    }
    .table_error_handler td{
        border: 1px solid rgba(0,0,0,0.25);
        padding: 5px;
    }
    .table_error_handler.notice td{
        color: #31708F;
        background-color: #D9EDF7;
        border-color: #BCE8F1;
    }
    .table_error_handler.warning td{
        color: #8A6D3B;
        background-color: #FCF8E3;
        border-color: #FAEBCC;
    }
    .table_error_handler.fatal-error td{
        color:#A94442;
        background-color: #F2DEDE;
        border-color: #EBCCD1;
    }
</style>