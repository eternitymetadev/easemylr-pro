<style>
.loadingComponent {
    color: official;
    display: inline-block;
    position: relative;
    width: 60px;
    height: 60px;
}

.loadingComponent div {
    transform-origin: 40px 40px;
    animation: loadingComponent 1.2s linear infinite;
}

.loadingComponent div:after {
    content: " ";
    display: block;
    position: absolute;
    top: 14px;
    left: 21px;
    width: 4px;
    height: 18px;
    border-radius: 20%;
    background: #000000;
}

.loadingComponent div:nth-child(1) {
    transform: rotate(0deg);
    animation-delay: -1.1s;
}

.loadingComponent div:nth-child(2) {
    transform: rotate(30deg);
    animation-delay: -1s;
}

.loadingComponent div:nth-child(3) {
    transform: rotate(60deg);
    animation-delay: -0.9s;
}

.loadingComponent div:nth-child(4) {
    transform: rotate(90deg);
    animation-delay: -0.8s;
}

.loadingComponent div:nth-child(5) {
    transform: rotate(120deg);
    animation-delay: -0.7s;
}

.loadingComponent div:nth-child(6) {
    transform: rotate(150deg);
    animation-delay: -0.6s;
}

.loadingComponent div:nth-child(7) {
    transform: rotate(180deg);
    animation-delay: -0.5s;
}

.loadingComponent div:nth-child(8) {
    transform: rotate(210deg);
    animation-delay: -0.4s;
}

.loadingComponent div:nth-child(9) {
    transform: rotate(240deg);
    animation-delay: -0.3s;
}

.loadingComponent div:nth-child(10) {
    transform: rotate(270deg);
    animation-delay: -0.2s;
}

.loadingComponent div:nth-child(11) {
    transform: rotate(300deg);
    animation-delay: -0.1s;
}

.loadingComponent div:nth-child(12) {
    transform: rotate(330deg);
    animation-delay: 0s;
}

@keyframes loadingComponent {
    0% {
        opacity: 1; }

    100% {
        opacity: 0;
    }
}
#themeLoader{
    display: none;
    align-items: center;
    justify-content: center;
    height: 100%;
    width: 100%;
    position: fixed;
    inset: 0;
    z-index: 99999999;
    background: radial-gradient(#83838360, transparent);
    pointer-events: stroke;
}
</style>

<div id="themeLoader" class="animate__animated animate__fadeIn">
    <div class="loadingComponent">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </div>
</div>