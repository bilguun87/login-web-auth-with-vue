﻿import { jsPDF } from "jspdf"
var callAddFont = function () {
this.addFileToVFS('timesbd-bold.ttf', font);
this.addFont('timesbd-bold.ttf', 'timesbd', 'bold');
};
jsPDF.API.events.push(['addFonts', callAddFont])