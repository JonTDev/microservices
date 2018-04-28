// Constant for easily storing variables.
const arg = { };

/**
 * Smart Parameters and accepts them in pairs or not at all.
 * Columns first then values.
 * Performs queries using mongoose-simpledb
 *
 * (column, value)
 *
 * @param {simpledb} db
 * @param {callback} cb
 */
function getMethod(db, cb) {

    // Placeholder for the column variables.
    let placeholder = '';

    // Checking if there are any arguments provided.
    if(process.argv.length > 0) {
        // If there are, asynchronously iterates through the
        // arguments provided
        db.forEachAsync(process.argv, (k, v, done) => {
            // Checking if the placeholder is empty.
            if(placeholder === '')

                // Column
                placeholder = v;

            // If it is the _id as wild card searches does not perform well
            // against _id columns.
            else if(placeholder === '_id') {

                // Setting hte _id
                arg[placeholder] = v;

                // Clearing the placeholder incase there are more options.
                placeholder = '';

                // Checking if it is a Number.  Some of the tests I ran
                // had some issues running the Number variables with
                // a wild care search.
            } else if(db.Orders.schema.obj[placeholder] === Number) {

                // Setting the Number variable.
                arg[placeholder] = v;

                // Setting up for next loop.
                placeholder = '';

            } else {

                // Setting a wild card search
                arg[placeholder] = new RegExp(v, 'i');

                // Setting up for next loop.
                placeholder = '';
            }

            // Checking if this is the last iteration.
            if(done() === false) {

                // Performing query and returning the results.
                db.Orders.find(arg, cb);
            }
        })
    } else {

        // Gathering entire collection and returning results.
        db.Orders.find({ }, cb);
    }
}

// Exporting module.
module.exports = getMethod;