
function intrabox_toggle() {
  document.getElementById('intraboxes').style.display=(document.getElementById('intraboxes').style.display=='none' || document.getElementById('intraboxes').style.display=='' || typeof(document.getElementById('intraboxes').style.display)!=='string'?'inline':'');
}
function intrabox_upd() {
  d=document.createElement('span');
  d.id='ibtoggle_btn';
  d.className='ib_toggle';
  d.class='ib_toggle';
  d.onclick=function() {document.getElementById('ibtoggle_btn').innerHTML=(document.getElementById('ibtoggle_btn').innerHTML=='Show intrabox'?'Hide intrabox':'Show intrabox');intrabox_toggle();};
  d.innerHTML='Show intrabox';
  document.getElementsByClassName('header')[0].appendChild(d);
  // if mobile, set width to 100% to allow for easier scrolling
  if(navigator.userAgent.match(MOBILE_REGEX||/Android|iOS|iPhone|iPod|Windows Phone/)!==null) {
    document.getElementById('intraboxes').style.width='100%';
  }
}
