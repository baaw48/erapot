const fs = require('fs');
const glob = require('glob');

glob('resources/js/Pages/*/Index.vue', (err, files) => {
    if (err) throw err;
    files.forEach(file => {
        let content = fs.readFileSync(file, 'utf8');
        content = content.replace(/class="animate-fade-in space-y-6 max-w-[a-zA-Z0-9\[\]\-]+ mx-auto pb-12"/g, 'class="animate-fade-in space-y-6 w-full px-4 sm:px-6 lg:px-8 mx-auto pb-12"');
        fs.writeFileSync(file, content);
        console.log('Updated ' + file);
    });
});
