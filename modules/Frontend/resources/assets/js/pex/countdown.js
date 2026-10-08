import $ from "../jquery.js";
import { PEX_CONFIG as CFG } from "./config.js";

/* العداد التنازلي لموعد الملتقى — hero.blade.php */
export function initCountdown() {
    var $wrap = $(".countdown-wrap");
    if (!$wrap.length) return;

    var target = new Date(CFG.EVENT_DATETIME || "2026-10-15T09:00:00+03:00").getTime();
    var $days = $("#cd-days");
    var $hours = $("#cd-hours");
    var $mins = $("#cd-mins");
    var $secs = $("#cd-secs");
    var timer = null;

    function pad(n) {
        return String(n).padStart(2, "0");
    }

    function tick() {
        var diff = target - Date.now();
        if (diff <= 0) {
            $wrap.hide();
            if (timer) clearInterval(timer);
            return;
        }
        var d = Math.floor(diff / 86400000);
        var h = Math.floor((diff % 86400000) / 3600000);
        var m = Math.floor((diff % 3600000) / 60000);
        var s = Math.floor((diff % 60000) / 1000);
        $days.text(pad(d));
        $hours.text(pad(h));
        $mins.text(pad(m));
        $secs.text(pad(s));
    }

    tick();
    timer = setInterval(tick, 1000);
}
