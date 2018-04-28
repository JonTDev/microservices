/**
 * This function accepts smart parameters.
 * The first parameter must be the ID.
 * All after that must be in pairs going column then value.
 *
 * This query is performed with mongoose-simpledb.
 *
 * (id, column, value)
 * @param db
 * @param cb
 */
function updateMethod(db, cb) {

    // Getting the id
    let id = process.argv.shift();

    // Preparing variables.
    let arg = {};
    let placeholder = '';

    // Asynchronously iterating through the provided arguments.
    db.forEachAsync(process.argv, (k, v, done) => {

        // Checking if the placeholder is empty.
        if(placeholder === '')

            // Setting the column to the placeholder.
            placeholder = v;

        else {

            // Setting the column and value
            arg[placeholder] = v;

            // Emptying the placeholder for the next iteration.
            placeholder = '';
        }

        // Checking if this is the last iteration.
        if (done() === false)

            // Finding and updating the record
            // using the arguments provided.
            db.Orders.update({ _id : id }, arg, (err, result) => {

                // Checking if it was successful.
                if(result.ok === 1)

                    // sending the results either way.
                    return cb(err, `Successfully updated ${id}`);
                else
                    return cb(err, `Failed to updated ${id}`);
            });
    });
}

// Exporting module
module.exports = updateMethod;