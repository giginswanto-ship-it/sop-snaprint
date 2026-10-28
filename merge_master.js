const fs = require('fs');
const path = require('path');

const masterPath = path.join(__dirname, 'BUNDEL_SOP_SNAPRINT_DIGITAL_PRINTING.md');
const formsPath = path.join(__dirname, 'LAMPIRAN_DOKUMEN_KONTROL_SOP_SNAPRINT.md');

let masterContent = fs.readFileSync(masterPath, 'utf8');
const formsContent = fs.readFileSync(formsPath, 'utf8');

// Find Bagian IV in master content
const bagianIVHeader = '# BAGIAN IV: FORMULIR, CHECKLIST, & LOGSHEET KONTROL OPERASIONAL';
const pengesahanHeader = '# LEMBAR PENGESAHAN DOKUMEN SISTEM';

const masterBeforeIV = masterContent.split(bagianIVHeader)[0];
const masterAfterIV = masterContent.split(pengesahanHeader)[1];

// Extract from formsContent starting from "# MASTER FORMAT DOKUMEN KONTROL OPERASIONAL RESMI"
const formsExtract = formsContent.split('# MASTER FORMAT DOKUMEN KONTROL OPERASIONAL RESMI')[1].split('# LEMBAR PENGESAHAN DOKUMEN KONTROL OPERASIONAL')[0];

const updatedBagianIV = `# BAGIAN IV: MASTER FORMULIR, CHECKLIST, & DOKUMEN KONTROL OPERASIONAL RESMI (26 SOP)

Berikut adalah master format formulir kontrol fisik lengkap (26 SOP) yang diturunkan dari point 6 masing-masing modul SOP, wajib dicetak dan diimplementasikan secara berkala pada operasional gerai resmi Snaprint Digital Printing:

${formsExtract.trim()}

---
`;

const newMasterContent = `${masterBeforeIV.trim()}\n\n${updatedBagianIV}\n\n# LEMBAR PENGESAHAN DOKUMEN SISTEM${masterAfterIV}`;

fs.writeFileSync(masterPath, newMasterContent, 'utf8');
console.log('Successfully updated BUNDEL_SOP_SNAPRINT_DIGITAL_PRINTING.md with all 26 control forms!');
