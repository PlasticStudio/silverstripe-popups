<% if $Popups %>
    <div class="sp-popups" data-count="{$Popups.Count}">
        <% loop $Popups %>
            <div
                class="sp-popup sp-popup--hidden sp-popup--mode-{$Mode.LowerCase}<% if $ExtraPopupClasses %> $ExtraPopupClasses<% end_if %>"
                data-popup-id="$ID"
                data-trigger="$Trigger"
                
                data-mode="{$Mode.LowerCase}"
                <% if $CollapseOnMobile %>data-collapse-on-mobile="1"<% end_if %>
            >
                <div
                    class="sp-popup__content sp-popup__content--$Position sp-popup__content--"
                    style="
                        width: $Width;
                        max-width: $MaxWidth;
                        <% if $Position == 'custom' && $PositionCustom %>
                            $PositionCustom
                        <% end_if %>
                    "
                >
                    <div class="sp-popup__minimized sp-popup--hidden <% if $AlignMinimizedRight %>sp-popup__minimized--right<% end_if %><% if $ExtraMinimizeClasses %> $ExtraMinimizeClasses<% end_if %>" data-popup-id="$ID"
                        style="<% if $PopupMinimizedTabColour %>background-color: $PopupMinimizedTabColour.rgbaColour;<% end_if %>">
                        <button class="sp-popup__close<% if $ExtraCloseClasses %> $ExtraCloseClasses<% end_if %>" data-popup-id="$ID"></button>
                        <p class="sp-popup__minimized-title"
                            data-popup-id="$ID" 
                            style="<% if $PopupMinimizedTabColour %>color: #{$PopupMinimizedTabColour.ContrastColour};<% end_if %>"
                        >
                            <% if $MinimizedTitle %>
                                $MinimizedTitle
                            <% else %>
                                $Title
                            <% end_if %>
                            $AlignMinimizedRight
                        </p>
                    </div>

                    <div class="sp-popup__full" data-popup-id="$ID">
                        <% if $EnableMinimize %>
                            <button class="sp-popup__minimize<% if $ExtraMinimizeClasses %> $ExtraMinimizeClasses<% end_if %>" data-popup-id="$ID"></button>
                        <% else %>
                            <button class="sp-popup__close<% if $ExtraCloseClasses %> $ExtraCloseClasses<% end_if %>" data-popup-id="$ID"></button>
                        <% end_if %>
                        <div class="sp-popup__inner">
                            <% include ContentItem %>
                        </div>
                    </div>
                </div>

                <% if $EnableBackdrop %>
                    <% if $BackdropMode == "colour" %>
                        <div class="sp-popup-backdrop" data-popup-id="$ID" style="background-color: $PopupBackdropColour.rgbaColour;"></div>
                    <% else_if $BackdropMode == "image" %>
                        <div class="sp-popup-backdrop" data-popup-id="$ID">
                            <% if $PopupBackdropImage %>
                                <div class="sp-popup-backdrop-image">
                                    <% with $ElementBackgroundImage %>
                                        <div class="sp-popup-backdrop-image__inner">
                                            <picture
                                                class="sp-popup-backdrop-image__picture"
                                                style="object-position: {$FocusPoint.PercentageX}% {$FocusPoint.PercentageY}%;"
                                            >
                                                <source
                                                media="(max-width: 500px)"
                                                type="image/webp"
                                                srcset="$FocusFill(500,300).Format('webp').URL">
                                                <source
                                                media="(max-width: 1200px)"
                                                type="image/webp"
                                                srcset="$FocusFill(1200,800).Format('webp').URL">
                                                <source
                                                media="(max-width: 2500px)"
                                                type="image/webp"
                                                srcset="$FocusFill(1900, 1400).Format('webp').URL">
                                                <img
                                                class="sp-popup-backdrop-image__picture-image"
                                                src="$FocusFill(1900,1400).URL"
                                                style="object-position: {$FocusPoint.PercentageX}% {$FocusPoint.PercentageY}%"
                                                loading="lazy"
                                                width="1900"
                                                height="1400"
                                                alt="$Image.Title.ATT">
                                            </picture>
                                        </div>
                                    <% end_with %>
                                </div>
                            <% end_if %>
                        </div>
                    <% else_if $BackdropMode == "video" %>
                        <div class="sp-popup-backdrop" data-popup-id="$ID">
                            <% if $PopupBackdropVideo %>
                                <div class="sp-popup-backdrop-video">
                                    <% with $PopupBackdropVideo %>
                                        <div class="sp-popup-backdrop-video__inner">
                                            <video 
                                                class="sp-popup-backdrop-video__video-player" 
                                                autoplay 
                                                loop 
                                                muted 
                                                playsinline
                                            >
                                                <source src="$URL" type="video/mp4" class="sp-popup-backdrop-video__video">
                                            </video>
                                        </div>
                                    <% end_with %>
                                </div>
                            <% end_if %>
                        </div>
                    <% end_if %>
                <% end_if %>
            </div>
        <% end_loop %>
    </div>
<% end_if %>