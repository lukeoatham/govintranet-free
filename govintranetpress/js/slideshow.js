var $$ = jQuery.fn;

$$.extend({
  SplitID : function()
  {
    return this.attr('id').split('-').pop();
  },

  Slideshow : {
    Ready : function()
    {
      jQuery('div.carouselcontrol')
        .hover(
          function() {
            jQuery(this).addClass('tmpSlideshowControlOn');
          },
          function() {
            jQuery(this).removeClass('tmpSlideshowControlOn');
          }
        )
        .click(
          function() {
            $$.Slideshow.Interrupted = true;

            jQuery('div.tmpSlide').hide();
            jQuery('div.carouselcontrol').removeClass('tmpSlideshowControlActive');

            jQuery('div#tmpSlide-' + jQuery(this).SplitID()).show()
            jQuery(this).addClass('tmpSlideshowControlActive');
          }
        );

      this.Counter = 1;
      this.Interrupted = false;

      this.Transition();
    },

    Transition : function()
    {
      if (this.Interrupted) {
        return;
      }

      this.Last = this.Counter - 1;

      if (this.Last < 1) {
        this.Last = jQuery('div.tmpSlide').length;
      }

      jQuery('div#tmpSlide-' + this.Last).fadeOut(
        'slow',
        function() {
          jQuery('div#carouselcontrol-' + $$.Slideshow.Last).removeClass('tmpSlideshowControlActive');
          jQuery('div#carouselcontrol-' + $$.Slideshow.Counter).addClass('tmpSlideshowControlActive');
          jQuery('div#tmpSlide-' + $$.Slideshow.Counter).fadeIn('slow');

          $$.Slideshow.Counter++;

          if ($$.Slideshow.Counter > jQuery('div.tmpSlide').length) {
            $$.Slideshow.Counter = 1;
          }

          setTimeout('$$.Slideshow.Transition();', 8000);
        }
      );
    }
  }
});

jQuery(document).ready(
  function() {
    $$.Slideshow.Ready();
  }
);