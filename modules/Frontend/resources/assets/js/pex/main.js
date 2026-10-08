import $ from "../jquery.js";
import { initCountdown } from "./countdown.js";
import { initAgendaAccordion } from "./agenda.js";
import { initShareButtons } from "./share-buttons.js";
import { initProfileModal } from "./profile-modal.js";

/* نقطة التجميع لكل سلوك JS الخاص بالصفحة الرئيسية (pex). كل ملف Blade
   يبني HTML القسم وبيانات PHP الخاصة به فقط — أي سلوك تفاعلي (عداد
   تنازلي، accordion، أزرار مشاركة، نافذة التعريف) يعيش هنا وفي ملفاته
   المساعدة (countdown.js / agenda.js / share-buttons.js /
   profile-modal.js) بدلاً من <script> داخل ملفات Blade. */
initCountdown();
initAgendaAccordion();
initShareButtons();
initProfileModal();
