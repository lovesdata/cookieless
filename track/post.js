var tid = setInterval (function ()
{
  if (document.readyState !== "complete")
  {
    return;
  }
  clearInterval (tid);
  
  var data = "uid=" + uid;
  
  data += "&ch=" + ch;
  data += "&scr_width=" + encodeURIComponent (HSTracking.get_width());  
  data += "&scr_height=" + encodeURIComponent (HSTracking.get_height());
  data += "&viewport_size=" + encodeURIComponent (HSTracking.get_viewport_size());
  data += "&flash=" + encodeURIComponent (HSTracking.check_flash());
  data += "&java=" + encodeURIComponent (HSTracking.check_java()); 
  data += "&title=" + encodeURIComponent (HSTracking.get_title());
  data += "&description=" + encodeURIComponent (HSTracking.get_description());
  data += "&encoding=" + encodeURIComponent (HSTracking.get_encoding()); 
  data += "&user_language=" + encodeURIComponent (HSTracking.get_user_language());
  data += "&url=" + encodeURIComponent (document.location);

  var receiver = document.createElement ("SCRIPT");
  receiver.setAttribute ("src", postUrl + "receiver.php?" + data);
  document.head.appendChild (receiver);
}, 100 );

var HSTracking =
{
  get_width : function ()
  {
    return window.screen.width;
  },
  
  get_height : function ()
  {
    return window.screen.height;
  },
  
  get_viewport_size : function ()
  {
    return document.documentElement.clientWidth + " x " + document.documentElement.clientHeight;
  },
  
  check_flash : function ()
  {
   try
   {
      try
      {
        var axo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash.6');
        try { axo.AllowScriptAccess = 'always'; }
        catch(e) { return '6,0,0'; }
      } catch(e) {}
        return new ActiveXObject('ShockwaveFlash.ShockwaveFlash').GetVariable('$version').replace(/\D+/g, ',').match(/^,?(.+),?$/)[1];
    } catch(e)
      {
        try
        {
          if(navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin)
          {
            return (navigator.plugins["Shockwave Flash 2.0"] || navigator.plugins["Shockwave Flash"]).description.replace(/\D+/g, ",").match(/^,?(.+),?$/)[1];
          }
        } catch(e) {}
      }
  return '0,0,0';
  },
  
  check_java : function ()
  {
    var resutl = null;
    for( var i=0,size=navigator.mimeTypes.length; i<size; i++ )
    {
      if( (resutl = navigator.mimeTypes[i].type.match(/^application\/x-java-applet;jpi-version=(.*)$/)) !== null )
          return resutl[1];
    }
    return null;
  },
  
  get_title : function ()
  {
    return document.title;
  },
  
  get_description : function ()
  {
    var metas = document.getElementsByTagName('meta'); 
  
     for (i=0; i<metas.length; i++)
     { 
       if (metas[i].getAttribute("name") == "description")
       { 
         return metas[i].getAttribute("content"); 
       } 
     } 
     return "";
  },
    
  get_encoding : function ()
  {
    try
    {
      if (document.charset !== undefined)
      {
        if (document.defaultCharset !== undefined)
        {
          return document.defaultCharset;  
        }
        else
        {
         return document.charset;
        }
      }
      else
      {
        return document.characterSet;
      }
    }
    catch(non_ie)
    {
      return document.inputEncoding || document.defaultCharset;
    }
  },
   
  get_user_language : function ()
  {
    var userLang = navigator.language || navigator.userLanguage;
    return userLang.substr (0, 2);
  }
}
