// tweaks for mobile browsers
MOBILE_REGEX=/Android|iOS|iPhone|iPod|Windows Phone/;
IS_MOBILE=navigator.userAgent.match(MOBILE_REGEX)!==null;

function mobiletweak() {
 // feel free to adjust regex if needed
  if(IS_MOBILE) {
    // code for mobile browsers goes here
  
  }
}
 
