<section id='search'>
    <div class='row'>
        <div class='col-1'></div>
        <div class='col-10'>
            <form action='all.php' method='get'>
            <input type='text' name='Search' placeholder="Search For Movie..." id='searchBar' class='searchBar' />
            <select name='genre' class='selectGenre'>
                <option value='' selected>All Genres</option>
                <?php
                    $dbh = connectToDatabase();
                    $stmt = $dbh->prepare("SELECT Genre FROM MovieGenre GROUP BY Genre");
                    $stmt->execute();
                    while ($row = $stmt->fetch()) {
                        $res = makeOutputSafe($row['Genre']);
                        echo '<option value="'. $res .'">'. $res .'</option>';
                    }
                ?>
            </select>
            </form>
        </div>
        <div class='col-1'></div>
    </div>
</section>