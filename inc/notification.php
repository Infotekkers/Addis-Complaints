<div id="global_notification_container"><?php echo $notification_message_content ?></div>

<style>
@import url("../style.css");

#global_notification_container {
    width: 450px;
    height: 120px;
    position: absolute;
    right: 0;
    top: 40px;
    background-color: red;
    display: flex;
    align-items: center;
    padding: 0 5px;
    z-index: 100;

}
</style>

<script>
setTimeout(() => {
    document.getElementById("global_notification_container").style.display = "none"
}, 3000);
</script>