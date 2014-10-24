<style type="text/css">
body > :not(.major-downtime) {
    margin-top: 100px;
}
@media (min-width: 920px) {
    body.login > * {
        margin-top: 0;
    }
    body.login > .major-downtime {
        width: 50%;
        width: calc(100% - 470px);
        top: 50%;
        transform: translateY(-50%);
        left: 470px;
        zoom: 1.2;
        height: auto;
        border: 2px solid black;
    }
    body.login > .major-downtime > div {
        padding: 20px 0;
    }
}
.major-downtime.pos {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100px;
    overflow-y: scroll;
}
.major-downtime > div {
    padding: 2px 10px;
    background-color: red;
    height: auto;
}
.major-downtime {
    background-color: white;
    z-index: 999;
    text-align: center;
}
.major-downtime .dtitle {
    font-size: 14px;
    font-weight: bold;
}
.major-downtime .ddetail {
    font-size: 12px;
}
</style>
<div class="major-downtime pos">
<div>
<span class="dtitle">Due to renovation related moves, all services provided by the
Computer Systems Lab will be offline this Saturday, 10/25 from 8AM until 6PM.</span><br />
<span class="ddetail">These services include Intranet, TJ email, the Library Database Proxy, and Remote Access. Other services, such as the main TJ website, will operate in a limited capacity. This downtime is necessary to move the various servers supporting these
services from their old server room in 115 to the new server room 200D. Thank you for your patience.
</span>
</div>
</div>
