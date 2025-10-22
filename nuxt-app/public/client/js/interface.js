(function ($) {
  "use strict";
  var TM = {};

  TM.toggleSearchSuggestion = () => {
    $(document).on("focus", ".searchInput", function (e) {
      $(".boxSuggestion").stop(true, true).slideDown(200);
    });
    $(document).on("blur", ".searchInput", function () {
      setTimeout(() => {
        $(".boxSuggestion").stop(true, true).slideUp(200);
      }, 150);
    });
  };

  TM.megaMenuHover = () => {
    const $menuItems = $(".menu-item");
    if ($menuItems.length === 0) return;
    let currentTab = $menuItems.first().data("tab");
    $(".tab-content").hide();
    $("#tab-" + currentTab).show();
    $menuItems.on("mouseenter", function () {
      const tab = $(this).data("tab");
      if (tab === currentTab) return;
      $(".tab-content").stop(true, true).hide();
      $("#tab-" + tab)
        .stop(true, true)
        .fadeIn(200);
      currentTab = tab;
    });
  };

  TM.getValueNameSearch = () => {
    $(document).on("click", ".name-search-keyword", function () {
      let keyword = $(this).text().trim();
      $(".searchInput").val(keyword);
      TM.autoSubmit("#searchHome");
    });
  };

  TM.autoSubmit = (formSelector) => {
    const $form = $(formSelector);
    if ($form.length) {
      $form.submit();
    }
    return false;
  };

  TM.countDown = (duration) => {
    let timer = duration;
    let hours, minutes, seconds;

    const countdown = setInterval(function () {
      hours = parseInt(timer / 3600, 10);
      minutes = parseInt((timer % 3600) / 60, 10);
      seconds = parseInt(timer % 60, 10);

      hours = hours < 10 ? "0" + hours : hours;
      minutes = minutes < 10 ? "0" + minutes : minutes;
      seconds = seconds < 10 ? "0" + seconds : seconds;

      $(".hour-count-down").text(hours);
      $(".minute-count-down").text(minutes);
      $(".second-count-down").text(seconds);

      if (--timer < 0) {
        clearInterval(countdown);
        $(".count-down").html("EXPIRED");
      }
    }, 1000);
  };

  TM.displayBacktoTop = (height) => {
    $(window).scroll(function () {
      if ($(this).scrollTop() > height) {
        $(".back-to-top").fadeIn();
      } else {
        $(".back-to-top").fadeOut();
      }
    });
  };

  TM.togglebtnViewMore = () => {
    let isExpanded = false;
    $(document).on("click", ".btnViewMoreAbout", function () {
      isExpanded = !isExpanded;
      if (isExpanded) {
        $(".about-data").removeClass("max-h-[300px]", "overflow-hidden");
      } else {
        $(".about-data").addClass("max-h-[300px]", "overflow-hidden");
      }
    });
  };

  TM.activeTab = () => {
    $(document).on("click", ".tabs_item", function () {
      $(".tabs_item").removeClass("active-tab");
      $(this).addClass("active-tab");
    });
  };

  TM.toggleViewContent = () => {
    $(".content-block").each(function () {
      const $block = $(this);
      const $container = $block.find(".contentContainer");
      const $toggleBtn = $block.find(".toggleBtn");
      const $btnText = $toggleBtn.find("span");
      let isExpanded = false;

      $toggleBtn.on("click", function () {
        const $overflow = $container.find(".overflow-blur-content");
        if (isExpanded) {
          $overflow.removeClass("hidden");
          $container.css("max-height", "653px").removeClass("expanded").css("overflow", "hidden");
          $btnText.text("Xem thêm");
        } else {
          $overflow.addClass("hidden");
          $container
            .css("max-height", $container.prop("scrollHeight") + "px")
            .addClass("expanded")
            .css("overflow", "visible");
          $btnText.text("Thu gọn");
        }

        isExpanded = !isExpanded;
      });
    });
  };

  TM.openModal = (modalId) => {
    $("#" + modalId)
      .removeClass("hidden")
      .addClass("flex");
  };

  TM.closeModal = (modalId) => {
    $("#" + modalId)
      .addClass("hidden")
      .removeClass("flex");
  };

  TM.toggleModal = (modalId) => {
    const $modal = $("#" + modalId);
    if ($modal.hasClass("hidden")) {
      TM.openModal(modalId);
    } else {
      TM.closeModal(modalId);
    }
  };

  TM.toggleEyePassword = (input = "password", icon = ".fa-eye, .fa-eye-slash") => {
    $(document).on("click", `#${input} + .toggle-show-password`, function () {
      const $input = $(`#${input}`);
      const $icon = $(this).find(icon);
      if ($input.attr("type") === "password") {
        $input.attr("type", "text");
        $icon.removeClass("fa-eye").addClass("fa-eye-slash");
      } else {
        $input.attr("type", "password");
        $icon.removeClass("fa-eye-slash").addClass("fa-eye");
      }
    });
  };

  TM.inputOtp = function ({ selector = "#inputOtp", onlyNumber = true } = {}) {
    const $inputs = $(selector);
    $(document).on("input", selector, function () {
      let $input = $(this);
      let value = $input.val();
      if (onlyNumber) {
        value = value.replace(/[^0-9]/g, "");
        $input.val(value);
      }
      if (value.length === 1) {
        const $next = $input.next(selector);
        if ($next.length) {
          $next.focus();
        }
      }
    });
    $(document).on("keydown", selector, function (e) {
      const $input = $(this);
      if (e.key === "Backspace" && $input.val() === "") {
        const $prev = $input.prev(selector);
        if ($prev.length) {
          $prev.focus();
        }
      }
    });
    $(document).on("focus", selector, function () {
      $(this).select();
    });
  };

  TM.registerModalEvents = () => {
    // Click nút mở modal
    $(document).on("click", "[data-open-modal]", function () {
      const modalId = $(this).data("open-modal");
      TM.openModal(modalId);
    });
    $(document).on("click", ".btn-close-modal", function () {
      const modalId = $(this).closest(".modal").attr("id");
      TM.closeModal(modalId);
    });
    // Click overlay để đóng modal
    $(document).on("click", ".modal-overlay", function () {
      const modalId = $(this).closest(".modal").attr("id");
      TM.closeModal(modalId);
    });
  };

  TM.init = () => {
    TM.toggleSearchSuggestion();
    TM.megaMenuHover();
    TM.getValueNameSearch();
    TM.displayBacktoTop(300);
    TM.togglebtnViewMore();
    TM.activeTab();
    TM.toggleViewContent();
    TM.toggleEyePassword();
    TM.registerModalEvents();
    TM.inputOtp();
  };

  $(document).ready(function () {
    TM.init();
  });

  window.TM = TM;
})(jQuery);
