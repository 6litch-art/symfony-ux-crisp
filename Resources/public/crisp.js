window.addEventListener("load", function()
{
    var crispData = document.getElementById("crisp-script").dataset;
    
    function getCookie(name) {
        var re = new RegExp(name + "=([^;]+)"); 
        var value = re.exec(document.cookie); 
        return (value != null) ? unescape(value[1]) : null; 
    }

    CRISP_RUNTIME_CONFIG = {session_merge : true, locale : crispData.locale};
    CRISP_WEBSITE_ID = crispData.websiteId;
    CRISP_TOKEN_ID = this.localStorage.getItem("crisp-client/token/"+CRISP_WEBSITE_ID) || 
                     getCookie("crisp-client%2Fsession%2F"+CRISP_WEBSITE_ID);

    this.localStorage.setItem("crisp-client/token/"+CRISP_WEBSITE_ID, CRISP_TOKEN_ID);

    if(typeof $crisp != "undefined")
        $crisp.push(["do", "session:reset", [false]]);

    window.$crisp = [];
    
    (function () {
        d = document;
        s = d.createElement("script");
        s.src = "https://client.crisp.chat/l.js";
        s.async = 1;
        d.getElementsByTagName("head")[0].appendChild(s);
    })(); 
});
