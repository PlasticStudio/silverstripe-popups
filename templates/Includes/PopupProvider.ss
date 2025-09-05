<% if $Popups %>
    <div class="wtk-popups" data-count="{$Popups.Count}">
        <% loop $Popups %>
            <div class="wtk-popup wtk-popup--hidden<% if $CustomClasses %> $CustomClasses<% end_if %>"
                data-popup-id="$ID"
                data-trigger="$Trigger"
                data-show-again-after="$ShowAgainAfter"
            >
                <div
                    class="wtk-popup__content wtk-popup__content--$Position wtk-popup__content--"
                    style="
                        width: $Width;
                        max-width: $MaxWidth;
                        <% if $Position == 'custom' && $PositionCustom %>
                            $PositionCustom
                        <% end_if %>
                    "
                >
                    <div class="wtk-popup__minimized wtk-popup--hidden <% if $AlignMinimizedRight %>wtk-popup__minimized--right<% end_if %>" data-popup-id="$ID"
                        style="<% if $PopupMinimizedTabColour %>background-color: $PopupMinimizedTabColour.rgbaColour;<% end_if %>">
                        <button class="wtk-popup__close" data-popup-id="$ID"></button>
                        <p class="wtk-popup__minimized-title"
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

                    <div class="wtk-popup__full" data-popup-id="$ID">
                        <% if $EnableMinimize %>
                            <button class="wtk-popup__minimize" data-popup-id="$ID"></button>
                        <% else %>
                            <button class="wtk-popup__close" data-popup-id="$ID"></button>
                        <% end_if %>
                        <div class="wtk-popup__inner">
                            <% include ContentItem %>
                        </div>
                    </div>
                </div>

                <% if $EnableBackdrop %>
                    <% if $BackdropMode == "colour" %>
                        <div class="wtk-popup-backdrop" data-popup-id="$ID" style="background-color: $PopupBackdropColour.rgbaColour;"></div>
                    <% else_if $BackdropMode == "image" %>
                        <div class="wtk-popup-backdrop" data-popup-id="$ID">
                            <% if $PopupBackdropImage %>
                                <div class="wtk-popup-backdrop-image">
                                    <% with $ElementBackgroundImage %>
                                        <div class="wtk-popup-backdrop-image__inner">
                                            <picture
                                                class="wtk-popup-backdrop-image__picture"
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
                                                class="wtk-popup-backdrop-image__picture-image"
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
                        <div class="wtk-popup-backdrop" data-popup-id="$ID">
                            <% if $PopupBackdropVideo %>
                                <div class="wtk-popup-backdrop-video">
                                    <% with $PopupBackdropVideo %>
                                        <div class="wtk-popup-backdrop-video__inner">
                                            <video 
                                                class="wtk-popup-backdrop-video__video-player" 
                                                autoplay 
                                                loop 
                                                muted 
                                                playsinline
                                            >
                                                <source src="$URL" type="video/mp4" class="wtk-popup-backdrop-video__video">
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