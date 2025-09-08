<% if $Popups %>
    <div class="sp-popups" data-count="{$Popups.Count}" data-cookie-expiry-time="{$Popups.First.CookieExpiryTime}">
        <% loop $Popups %>
            <div
                class="sp-popup sp-popup--hidden sp-popup--mode-{$Mode.LowerCase}<% if $ExtraPopupClasses %> $ExtraPopupClasses<% end_if %>"
                data-popup-id="$ID"
                data-sort-order="$PopupSortOrder"
                data-mode="{$Mode.LowerCase}"
                data-show-after="$ShowAfter"
                <% if $CollapseOnMobile %>data-collapse-on-mobile="1"<% end_if %>
            >
                <div class="sp-popup__content">
                    <div class="sp-popup__minimized sp-popup--hidden<% if $ExtraMinimizeClasses %> $ExtraMinimizeClasses<% end_if %>" data-popup-id="$ID">
                        <button class="sp-popup__close<% if $ExtraCloseClasses %> $ExtraCloseClasses<% end_if %>" data-popup-id="$ID"></button>
                        <p class="sp-popup__minimized-title" data-popup-id="$ID">
                            <% if $MinimizedTitle %>
                                $MinimizedTitle
                            <% else %>
                                $Title
                            <% end_if %>
                        </p>
                    </div>

                    <div class="sp-popup__full" data-popup-id="$ID">
                        <% if $EnableMinimize %>
                            <button class="sp-popup__minimize<% if $ExtraMinimizeClasses %> $ExtraMinimizeClasses<% end_if %>" data-popup-id="$ID"></button>
                        <% else %>
                            <button class="sp-popup__close<% if $ExtraCloseClasses %> $ExtraCloseClasses<% end_if %>" data-popup-id="$ID"></button>
                        <% end_if %>
                        <div class="sp-popup__inner">
                            <% if $Image %>
                                <div class="sp-popup__image">
                                    <img src="$Image.FocusFill(400,300).URL" alt="$Image.Title.ATT" />
                                </div>
                            <% end_if %>

                            <div class="sp-popup__inner-content">
                                <% if $Title %>
                                    <h3 class="sp-popup__title">$Title</h3>
                                <% end_if %>

                                <% if $Content %>
                                    <div class="sp-popup__content-text">
                                        $Content
                                    </div>
                                <% end_if %>

                                <% if $Links.exists %>
                                    <div class="sp-popup__links">
                                        <% loop $Links %>
                                            <% if $LinkURL %>
                                                <a href="$LinkURL" class="sp-popup__link<% if $ExtraClasses %> $ExtraClasses<% end_if %>"<% if $OpenInNew %> target="_blank" rel="noopener"<% end_if %>>
                                                    $Title
                                                </a>
                                            <% end_if %>
                                        <% end_loop %>
                                    </div>
                                <% end_if %>
                            </div>
                        </div>
                    </div>
                </div>

                <% if $Mode == 'modal' %>
                    <div class="sp-popup-backdrop" data-popup-id="$ID"></div>
                <% end_if %>
            </div>
        <% end_loop %>
    </div>
<% end_if %>