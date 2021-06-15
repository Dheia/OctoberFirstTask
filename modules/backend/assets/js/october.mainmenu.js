/*
 * Main menu
 *
 * Dependences:
 * - ResponsiveMenu (october.responsivemenu.js)
 */

+function ($) { "use strict";
    function MainMenu() {
        var $mainMenuElement = $('#layout-mainmenu .navbar ul.mainmenu-items'),
            $mainMenuToolbar = $('#layout-mainmenu [data-control=toolbar]'),
            $menuContainer = $mainMenuElement.closest('.layout-row'),
            menuHeight = $menuContainer.closest('.main-menu-container').outerHeight(),
            responsiveMenu = new $.oc.responsiveMenu(hideMenus),
            $overlay = null

        function init() {
            $mainMenuElement.on('click', 'li.has-subitems', onItemClick)
            $mainMenuElement.on('click', '.mainmenu-toggle', onShowResponsiveMenuClick)

            var dragScroll = $mainMenuToolbar.data('oc.dragScroll')
            if (dragScroll) {
                dragScroll.goToElement($('> li.active', $mainMenuElement), undefined, {'duration': 0})
            }
        }

        function displaySubmenu($li) {
            var submenuIndex = $li.data('submenuIndex'),
                $submenu = $menuContainer.find('.mainmenu-submenu-dropdown[data-submenu-index='+submenuIndex+']'),
                menuLeft = $li.offset().left

            getOverlay().addClass('show')
            $submenu.css({
                top: menuHeight,
                left: menuLeft
            })

            $submenu.addClass('invisible')
            $submenu.addClass('show')

            var menuRight = menuLeft + $submenu.width(),
                windowWidth = $(window).width()

            if (menuRight > windowWidth - 20) {
                $submenu.css({
                    left: menuLeft - (menuRight - windowWidth) - 20
                })
            }

            $submenu.removeClass('invisible')
            addKeyListener()
        }

        function onKeyDown(ev) {
            if (ev.keyCode == 27) {
                hideMenus()
            }
        }

        function addKeyListener() {
            $(document).on('keydown.mainmenusubmenu', onKeyDown)
        }

        function removeKeyListener() {
            $(document).off('.mainmenusubmenu')
        }

        function hideSubmenu() {
            $menuContainer
                .find('.mainmenu-submenu-dropdown.show')
                .removeClass('show')
        }

        function hideMenus() {
            getOverlay().removeClass('show')

            hideSubmenu()
            responsiveMenu.hide()

            removeKeyListener()
        }

        function getOverlay() {
            if ( $overlay ) {
                return $overlay
            }

            $overlay = $('<div class="mainmenu-submenu-overlay"></div>')
                .appendTo(document.body)

            $overlay.on('click', hideMenus)

            return $overlay
        }

        function onItemClick(ev) {
            var $li = $(ev.currentTarget).closest('li')

            ev.preventDefault()

            if ($(document.body).hasClass('drag')) {
                return false
            }

            displaySubmenu($li)
            return false
        }

        function onShowResponsiveMenuClick(ev) {
            ev.preventDefault()

            addKeyListener()
            getOverlay().addClass('show')
            responsiveMenu.show()
            return false
        }

        init()
    }

    $(document).ready(function(){
        $.oc.mainMenu = new MainMenu()
    })
}(window.jQuery);