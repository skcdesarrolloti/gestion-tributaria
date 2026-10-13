const reference = require('./resources/theme/comerciales.cjs');
module.exports = {
    ...reference,
    content: ['./app/Views/**/*.php', './public/**/*.php'],
};
