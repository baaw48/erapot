const fs = require('fs');
const path = require('path');
const dir = 'c:/laragon/www/E RAPOT/resources/js/Pages';

function walk(dir) {
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach(function(file) {
        file = path.join(dir, file);
        const stat = fs.statSync(file);
        if (stat && stat.isDirectory()) { 
            results = results.concat(walk(file));
        } else if(file.endsWith('.vue')) {
            results.push(file);
        }
    });
    return results;
}

const files = walk(dir);
let changedCount = 0;
files.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    let original = content;
    
    // Speed up animations
    content = content.replace(/animation:\s*fadeInAnim\s*0\.[45]s/g, 'animation: fadeInAnim 0.15s');
    content = content.replace(/animation:\s*slideUp\s*0\.[45]s/g, 'animation: slideUp 0.15s');
    content = content.replace(/animation-delay:\s*0\.1s;/g, 'animation-delay: 0.05s;');
    
    if (content !== original) {
        fs.writeFileSync(file, content);
        changedCount++;
    }
});
console.log('Replaced animations in ' + changedCount + ' files.');
