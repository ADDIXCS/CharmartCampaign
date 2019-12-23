/*
 * Create clickToggle function
 * Usage - Once Click the "trigger element"  toggle class Will add in to
 *         "target element"
 */
function clickToggle(clEle, tarWrap, adCl) {
  var targetWrapper = $(tarWrap), // Get target element
      clickElement = $(clEle), // Get trigger element
      activeClass = adCl;  // Toggle class

  // Function for "trigger element"
  clickElement.click(function () {
    // Add "toggle class" to "Target wrapper"
    targetWrapper.toggleClass(activeClass);
  });
}

/*
 * Create scrollheaderFixed function
 * Usage - Add class once window scroll down
 */
function scrollheaderFixed() {
  // "window" scroll function
  $(window).scroll(function () {
    // Condition apply - Sroll Top value > 100
    if ($(this).scrollTop() > 100) {
      // Add "header-fixed" class
      $('header').addClass('header-fixed');
    } else {
      // Remove "header-fixed" class
      $('header').removeClass('header-fixed');
    }
  });
}

/*
 * Create bannerArrow function
 * Usage - Once click the "trigger element"
 *         USer can navigate in to target position
 */
function bannerArrow(CLeLE, tARSeC, oFFSETvAL) {
  var targetSection = $(tARSeC), // Get Target section
      clickElement = $(CLeLE), // Get click element
      offsetValue = oFFSETvAL; // Offset value from top

  //Create function for "click element"
  clickElement.click(function (e) {
    e.preventDefault();
    // Animation
    $('html, body').animate({
      scrollTop: targetSection.offset().top - offsetValue
    }, 1000);
  });
}

/*
 * Create letSworktogetherLabelAlignment function
 */
function letSworktogetherLabelAlignment() {

  // Crete variable for target input
  var $eachInput = $('.section3 input.input-text');

  // OneKeyup function for target input
  $eachInput.focus(function () {
    var $this = $(this);
    // Add class for closest ".display-block" element
    $this.closest('.display-block').addClass('label-active');
    //Condition Apply - If "taget input" does'nt has value'
    if ($this.val() != " ") {
      $this.closest('.display-block').addClass('label-active');
    }
    else {
      $this.closest('.display-block').removeClass('label-active');
    }
  });

  // $eachInput.focusout(function () {
  //   var $this = $(this);
  //   if (!$this.val()) {
  //     $this.closest('.display-block').removeClass('label-active');
  //   }
  // });

  var $eachInput = $('.section3 .input-text-message');

  // OneKeyup function for target input
  $eachInput.focus(function () {
    var $this = $(this);
    // Add class for closest ".display-block" element
    $this.closest('.display-block').addClass('label-active');
    //Condition Apply - If "taget input" does'nt has value'
    if ($this.val() != " ") {
      $this.closest('.display-block').addClass('label-active');
    }
    else {
      $this.closest('.display-block').removeClass('label-active');
    }
  });

}

/*
 * Fit the banner section in full screen at the beginning.
 */
function bannerSectionInFullScreen() {
  /*  .section1 height  getting function */
/*
  var windowHeight = $(window).height(),
      headerHeight = $('header').height(),
      section1Height = windowHeight - headerHeight;

  $('body .section1').height(section1Height);

  bannerArrow('.section1 .down-arrow', '.section2', headerHeight);
  */
}

$(document).ready(function () {


    $('.strategy-and-insight-wrapper').hide();

    /*Form Section Popup*/

    $('.various').click(function() {
      alert("hello...")
        $(".team-popup").fancybox({
          maxWidth : 580,
          maxHeight : 580,
          fitToView : false,
          width  : '100%',
          height  : '100%',
          autoSize : false,
          closeClick : false,
          openEffect : 'none',
          closeEffect : 'none'
        });
    })

  /* iPad and Mobile sliding menu */
  clickToggle('header .menu-icon', 'body', 'top-menu-active');
  clickToggle('.menu ul li a', 'body', 'top-menu-active');

  /*  Fix header adding active class */
  scrollheaderFixed();

  bannerSectionInFullScreen();

  $(window).resize(function () {
    bannerSectionInFullScreen();
  });


  /* Lets work together section form */
  letSworktogetherLabelAlignment();

  $('.image-block').hover(function () {
        var $this = $(this);
        if (!$this.hasClass('active'))
          $this.addClass("hover");

      }, function () {
        var $this = $(this);
        $this.removeClass("hover");
      }
  );

  $('.image-block').on('click', function (e) {
    e.preventDefault();
    var $this = $(this);

    $('.image-block').removeClass('active');
    $this.removeClass("hover").addClass('active');

    $('.toggle-row').hide();
    $('.case-study-details').removeClass('show-mobile');

    if ($this.data("position") === "top") {
      var viewNo = $this.data("view"),
          $toggleRowTop = $('.toggle-row.top'),
          $relatedView = $("#st" + viewNo + "-desc");
      $relatedView.addClass('show-mobile');
      $toggleRowTop.empty();
      $toggleRowTop.append($relatedView.children().clone(true, true));
      $toggleRowTop.show();
      $('html, body').animate({
        scrollTop: $this.offset().top - 100
      }, 1000);
      $("body").delegate(".toggle-row .close", "click", function () {
        closeToggleRow();
      });
    }
    else if ($this.data("position") === "bottom") {
      var viewNo = $this.data("view"),
          $toggleRowBottom = $('.toggle-row.bottom'),
          $relatedView = $("#st" + viewNo + "-desc");
      $relatedView.addClass('show-mobile');
      $toggleRowBottom.empty();
      $toggleRowBottom.append($relatedView.children().clone(true, true));
      $toggleRowBottom.show();
      $('html, body').animate({
        scrollTop: $this.offset().top - 100
      }, 1000);
      $("body").delegate(".toggle-row .close", "click", function () {
        closeToggleRow();
      });
    }
  });


  $('.toggle-row .close').click(function () {
    closeToggleRow();
  });

  $('.image-block .close-link').click(function (e) {
    closeToggleRow();
    //close link in mobile views.
    e.stopPropagation();
    closeMobile();
  });

  //activates the close button in mobile views.
  $('.case-study-details .close').click(function () {
    closeMobile();
  });
  //

  function closeToggleRow() {
    $('.toggle-row').slideUp(function () {
      $('.image-block').removeClass('active');
    });
  }

  function closeMobile() {
    $('.case-study-details').removeClass('show-mobile');
    $('.image-block').removeClass('active');
  }

  //Change the slider background
  $('.slider').click(function () {
    $(this).toggleClass('off-bg');
    //action on slider
    if ($('#check').is(':checked')) {
      $('.demand-generation-wrapper').show();
      $('.demand-generation-wrapper .selected-tab a').click();
      $('.strategy-and-insight-wrapper').hide();
    }
    else {
      $('.strategy-and-insight-wrapper').show();
       $('.strategy-and-insight-wrapper .selected-tab a').click();
      $('.demand-generation-wrapper').hide();
    }
  });

  // tabs at the "we unify" section & make first tab selected at the initial state
  $('.select-tab').on('click', function () {
    var $this = $(this);
    $this.parent().addClass('selected-tab').siblings().removeClass('selected-tab');
    $('.tab_info').hide('slow');
    $('#tab_info_' + $this.data('target')).show('slow');
    // This disables firing the click event handler again and again clicking on the same item.
    $('.select-tab').parent().css("pointerEvents", "auto");
    $this.parent().css("pointerEvents", "none");
  }).first().click();

  // the top navigation
  $(".header-work").click(function (e) {
    e.preventDefault();
    $('.menu li a').removeClass('active');
    $(this).addClass('active');
    $('html, body').animate({
      scrollTop: $(".work-section").offset().top - 80
    }, 1000);
  });

  $(".header-services").click(function (e) {
    e.preventDefault();
    $('.menu li a').removeClass('active');
    $(this).addClass('active');
    $('html, body').animate({
      scrollTop: $(".services-section").offset().top - 80
    }, 1000);
  });

  $(".header-about").click(function (e) {
    e.preventDefault();
    $('.menu li a').removeClass('active');
    $(this).addClass('active');
    $('html, body').animate({
      scrollTop: $(".about-us-section").offset().top - 80
    }, 1000);
  });

  $(".header-contact").click(function (e) {
    e.preventDefault();
    $('.menu li a').removeClass('active');
    $(this).addClass('active');
    $('html, body').animate({
      scrollTop: $(".section3").offset().top -80
    }, 1000);
  });

/*
  $(".logo").click(function (e) {
    e.preventDefault();
    $('.menu li a').removeClass('active');
    $('html, body').animate({
      scrollTop: $(".wrapper").offset().top
    }, 1000);
  });
*/

  $(document).ready(function(){
    var pageURL = $(location).attr('href');
    if(pageURL.includes("#")){
      var split = pageURL.split("#");
      var sectionClass = "."+split[1];
      if(sectionClass=='.header-about'){
        goToAboutUs();
      }else{
        $(sectionClass).trigger( "click" );
      }

    }

  });

  function goToAboutUs() {
    $('.menu li a').removeClass('active');
    $(this).addClass('active');
    $('html, body').animate({
      scrollTop: $(".about-us-section").offset().top - 1004
    }, 1000);
  };

  /*Text Animations*/

  $(".element-call-1").typed({
    strings: ["CAMPAIGN", "USERS", "MARKETING", "SALES"],
    typeSpeed: 200,
    startDelay: 0,
    backSpeed: 0,
    contentType: 'html',
    loop: true
  });

});
