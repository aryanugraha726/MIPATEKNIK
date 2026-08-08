const js = `
function getBarY(index) {
    let header_height = 55;
    let padding = 10;
    let bar_height = 20;
    let step = bar_height + padding;
    // from frappe-gantt source:
    return header_height + padding + (index * step);
}
console.log(getBarY(0));
`;
require('fs').writeFileSync('test.js', js);
