<?php
/*
Plugin Name: SVN Update
Plugin URI: http://sunfox.org/wordpress-svn-update/
Description: Easily update a WordPress site under SVN.
Version: 1.0
Author: Sunny Ripert
Author URI: http://sunfox.org/
*/

/*  Copyright 2008 Sunny Ripert <negatif@gmail.com>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action('admin_menu', 'svn_update_pages');

function svn_update_pages() {
    add_options_page('SVN Update', 'SVN Update', 8, __FILE__, 'svn_update_page');
}

function svn_update_page() {
  $info = svn_update_svn_info();
?>
  <div class="wrap">
    <h2><?php _e('SVN Update') ?></h2>

    <?php if (isset($_GET['update'])) : ?>
    
    <h3><?php _e('Updating…') ?></h3>
    <pre><?php svn_update_svn_update($_GET['branch']) ?></pre>
    
    <?php else : ?>
    
    <form method="get" action="options-general.php">
      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row"><?php _e('Revision') ?></th>
            <td>
              <?php echo htmlspecialchars($info['revision']) ?>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php _e('Branch') ?></th>
            <td>
              <input type="text" id="branch" name="branch" size="62" value="<?php echo htmlspecialchars($info['branch']) ?>" />
            </td>
          </tr>
        </tbody>
      </table>

      <p class="submit">
        <input type="hidden" name="page" value="<?php echo htmlspecialchars($_GET['page']) ?>" />
        <input type="submit" name="update" value="<?php _e('Update') ?>" />
      </p>            

    </form>
    
    <?php endif; ?>
  </div>
<?
}

// Returns array with revision number and URL to current branch
function svn_update_svn_info() {
  $result = array();
  $info = shell_exec('svn info ..');
  if (preg_match('/Revision: (.*)/', $info, $matches))
    $result['revision'] = intval($matches[1]);
  if (preg_match('/URL: (.*)/', $info, $matches))
    $result['branch'] = $matches[1];
  return $result;
}

// Updates and switches to the new given branch
function svn_update_svn_update($branch = '') {
  $info = svn_update_svn_info();
  if ($branch && $info['branch'] != $branch)
    passthru('svn switch ' . escapeshellarg($branch) . ' ..');
  passthru('svn update ..');

}

