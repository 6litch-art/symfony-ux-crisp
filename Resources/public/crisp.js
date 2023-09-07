window.addEventListener("load", function()
{
    function uniqid(prefix = "", random = false) {
        const sec = Date.now() * 1000 + Math.random() * 1000;
        const id = sec.toString(16).replace(/\./g, "").padEnd(14, "0");
        return `${prefix}${id}${random ? `.${Math.trunc(Math.random() * 100000000)}`:""}`;
    };

    var crispData = document.getElementById("crisp-script").dataset;

    window.CRISP_RUNTIME_CONFIG = {session_merge : true, locale : crispData.locale};
    window.CRISP_WEBSITE_ID = crispData.websiteId;
    window.CRISP_TOKEN_ID = crispData.userId || this.localStorage.getItem("crisp-client/token/"+window.CRISP_WEBSITE_ID);
    if(window.CRISP_TOKEN_ID == null) window.CRISP_TOKEN_ID = uniqid("", true);

    this.localStorage.setItem("crisp-client/token/"+window.CRISP_WEBSITE_ID, window.CRISP_TOKEN_ID);

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
