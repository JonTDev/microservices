/**
 * Using the mongoose-simpledb to perform queries
 * against the mongodb database.
 * First parameter will always be the id.
 *
 * Everything else must be in pairs.  Going Column then Value.
 *
 * (id, column, value)
 * @param {simpledb} db
 * @param {callback} cb
 */
function updateMethod(db, cb) {

    // Getting the ID
    let id = process.argv.shift();

    // Preparing variables
    let arg = {};
    let placeholder = '';

    // Asynchronously iterates through arguments provided.
    db.forEachAsync(process.argv, (k, v, done) => {

        // If the placeholder is empty it will store it.
        if(placeholder === '')
            // Column
            placeholder = v;
        else {
            // Storing the value with a key as the column.
            arg[placeholder] = v;

            // Emptying the placeholder for next iteration.
            placeholder = '';
        }

        // Checking if this is the last loop
        if (done() === false)

            // Updating the record with the ID using the arguments found.
            db.Items.update({ _id : id }, arg, (err, itemId) => {

                // Checking it was successful and returning its findings.
                if(itemId.ok === 1)
                    return cb(err, `Successfully updated ${id}`);
                else
                    return cb(err, `Failed to updated ${id}`);
            });
    });
}

// Exporting this module.
module.exports = updateMethod;