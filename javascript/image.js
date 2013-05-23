(function($) {
   GetImageColumns = function(NumberofImages){
        var obj = { hb : 1, vb : 1};
        var a = 1, b = 1,sum = a * b;
        while(sum<NumberofImages)
          {
            if(a == b)
              a++;
            else if(a>b)
              b++;
             sum = a * b;
          }
          obj.hb = b;
          obj.vb = a;
        return obj;
   };
   GetRatio = function(x,y)
   {
     var calc= function(){
        var num1,num2;
        if(x < y){ 
            num1=x;
            num2=y;  
         }
         else{
            num1=y;
            num2=x;
          }
        var remain=num2%num1;
        while(remain>0){
            num2=num1;
            num1=remain;
            remain=num2%num1;
        }
        return num1;    
     };
     var gcd = calc();
     return (x/gcd)+":"+(y/gcd);
   };
   LoadImages = function(Options){
        var obj = GetImageColumns(Options.ImagesCount);
        var width = Options.Width / obj.vb ;
        var height = Options.Height / obj.hb ;
        $(Options.Element).each(function(){
            $(this).fadeIn().css({'width':width+'px','height':height+'px','float':'left'}).attr('class','list-item');
            var img = $(this).find('img');  
            var imgwidth = width,imgheight = height;
            $(img).attr('src',"image.php?width="+imgwidth+"&height="+imgheight+"&cropratio="+GetRatio(imgwidth,imgheight)+"&image=/MVCCollage/"+$(img).attr('url'));
        });
   };
   $.Image = function(options){
       var Image_fun = {
           options: $.extend($.Image.defaults,options),
           load : function(){
               $(window).load(function(){
                  LoadImages(Image_fun.options) 
               });
           }
       };
       return {
           loadImages : Image_fun.load
       };
   };
   $.Image.defaults ={
       Width :800,
       Height : 800,
       ImagesCount : 1,
       Element : ""
   };
})(jQuery);


