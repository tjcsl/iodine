$(function(){
    $("body *").css("font-family", "'Times'");
    function glitch() {
        setTimeout(function() {
            var sizes = {};
            $("*").not("#bsod, #bsod *").each(function(i) {
                sizes[i] = $(this).css("font-size");
                $(this).css("font-size", "5px");
            });
            setTimeout(function() {
                $("*").not("#bsod, #bsod *").each(function(i) {
                    $(this).css("font-size", sizes[i]);
                });
            }, 10);
        }, (Math.floor(Math.random() * 500) + 250));
    }

    function addDots(sel) {
        setTimeout(function() {
            $(sel).html(".")
            setTimeout(function() {
                $(sel).html("..");
                setTimeout(function() {
                    $(sel).html("...")
                }, (Math.floor(Math.random() * 500) + 1000));
            }, (Math.floor(Math.random() * 300) + 700));
        }, (Math.floor(Math.random() * 300) + 400));
    }

    $("#big-red-button").click(function(e) {
        $(this).unbind("click");

        
        window.defaultDisplay = {};
        $("p, input, button, h1, h2,h3, h4, td").hover(function(i) {
            window.defaultDisplay[i] = $(this).css("display");
            $(this).css("display", "none");
        }, function(i) {
           $(this).css("display", window.defaultDisplay[i]);
        });
        

        
        setInterval(function(i) {
            if (window.bsod == null)
                glitch();
        }, 1000)
       


        setTimeout(function() {
            window.bsod == true;
            setTimeout(function() {


                $("body").html("\
                    <style>\
                        html, body {\
                            cursor:progress;\
                            cursor:wait;\
                            -moz-user-select: none;\
                            -webkit-user-select: none;\
                            -ms-user-select: none;\
                        }\
                        #bsod {\
                            background-color: #0102ac;\
                            top: 0;\
                            left: 0;\
                            right: 0;\
                            bottom: 0;\
                            position: fixed;\
                            z-index: 999999;\
                            padding: 1em;\
                            white-space:nowrap;\
                        }\
                        #bsod * {\
                            color: #fff;\
                            font-family: 'lucida console', 'courier';\
                            font-size: 18px;\
                            font-weight: normal;\
                        }\
                        p {\
                            margin-top: 0;\
                            margin-bottom: 1em;\
                        }\
                    </style>\
                    <div id='bsod'>\
                        <p>A problem has been detected and Windows has been shut down to prevent damage <br />to your computer.</p>\
                        <p>The problem seems to be caused by the following file: INTRANET.EXE</p>\
                        <p>PAGE_FAULT_IN_NONPAGED_AREA</p>\
                        <p>If this is the first time you've seen this Stop error screen, <br />restart your computer. If this screen appears again, <br />follow these steps:</p>\
                        <p>Check to make sure any new hardware or software is properly installed. <br />If this is a new installation, ask your hardware or software manufacturer <br />for any Windows updates you might need.</p>\
                        <p>If problems continue, disable or remove any newly installed hardware <br />or software. Disable BIOS memory options such as caching or shadowing. <br />If you need to use Safe Mode to remove or disable components, restart <br />your computer, press F8 to select Advanced Startup Options, and then <br />select Safe Mode.</p>\
                        <p>Technical information:</p>\
                        <p>*** STOP: 0X00000050 (0xFD3094C2,0x00000001,0xFBFE7617,0x00000000)</p>\
                        <p style='margin-top:2em;''>***&nbsp;&nbsp;INTRANET.EXE &ndash; Address FBFE7617 base at FBFE5000, DateStamp 3d6dd67c</p>\
                        <p id='block1' style='display:none'>Collecting data for crash dump <span id='dots1'></span>\
                        <span id='block2' style='display:none'><br />Initializing disk for crash dump <span id='dots2'></span>\
                        <span id='block3' style='display:none'><br />Beginning dump of physical memory<br />Dumping physical memory to disk <span id='dots3'></span></span>\
                        <span id='block4' style='display:none'><br />Physical memory dump complete.<br />Contact your system admin or technical support group for further assistance.</span></p>\
                    </div>\
                ");

                setTimeout(function() {
                    $("#block1").show();
                    addDots("#dots1");
                    setTimeout(function() {
                        $("#block2").show();
                        addDots("#dots2");
                        setTimeout(function() {
                            $("#block3").show();
                            addDots("#dots3");
                            setTimeout(function() {
                                $("#block4").show();
                            }, 6000);
                        }, 6000);
                    }, 6000);
                }, 3000);
            }, 1000);
        }, 5000);
    });
});
