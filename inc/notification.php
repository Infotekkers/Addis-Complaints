<div id="global_notification_container">
    <div class="notification-decoration">
        <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 35 35">
            <g id="Group_456" data-name="Group 456" transform="translate(984 1236)">
                <circle id="Ellipse_20" data-name="Ellipse 20" cx="17.5" cy="17.5" r="17.5" transform="translate(-984 -1236)" fill="var(--error-50)" />
                <g id="Group_454" data-name="Group 454" transform="translate(-3256.5 -3316.5)">
                    <line id="Line_9" data-name="Line 9" x2="15" y2="15" transform="translate(2282.5 2090.5)" fill="none" stroke="var(--error-600)" stroke-width="5" />
                    <line id="Line_10" data-name="Line 10" x2="15" y2="15" transform="translate(2297.5 2090.5) rotate(90)" fill="none" stroke="var(--error-600)" stroke-width="5" />
                </g>
            </g>
        </svg>
    </div>
    <div class="notification-content">
        <h1>Error</h1>
        <p>
            <?php echo $notification_message_content ?>.
        </p>
    </div>
</div>

<style>
    @import url("../style.css");

    :root {
        --error-50: #fef2f2;
        --error-100: #fee2e2;
        --error-200: #fecaca;
        --error-300: #fca5a5;
        --error-400: #f87171;
        --error-500: #ef4444;
        --error-600: #dc2626;
        --error-700: #b91c1c;
        --error-800: #991b1b;
        --error-900: #7f1d1d;
    }

    #global_notification_container {
        width: 350px;
        position: absolute;
        right: var(--spacing-regular);
        top: 48px;

        background: var(--error-50);
        color: var(--error-900);
        font-family: var(--primary-font);

        border-radius: 2px;

        display: flex;
        align-items: center;

        z-index: 100;
    }


    #global_notification_container h1 {
        font-size: var(--text-xs);
        font-weight: 600;
        color: var(--error-900);
    }

    #global_notification_container .notification-decoration {
        padding: var(--spacing-base) var(--spacing-small);
        background: var(--error-600);
        border-radius: 2px;
        margin-right: var(--spacing-xsmall);
    }

    #global_notification_container svg {
        width: 25px;
    }
</style>

<script>
    setTimeout(() => {
        document.getElementById("global_notification_container").style.display = "none"
    }, 3000);
</script>