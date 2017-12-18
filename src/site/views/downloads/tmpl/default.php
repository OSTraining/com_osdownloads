<?php
/**
 * @package   OSDownloads
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016-2017 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die( 'Restricted access' );

use Alledia\Framework\Helper as AllediaHelper;
use Alledia\OSDownloads\Free\Factory;

$app                    = JFactory::getApplication();
$lang                   = JFactory::getLanguage();
$doc                    = JFactory::getDocument();
$container              = Factory::getContainer();
$numberOfColumns        = (int)$this->params->get("number_of_column", 1);
$user                   = JFactory::getUser();
$authorizedAccessLevels = $user->getAuthorisedViewLevels();
$itemId                 = $app->input->getInt('Itemid');
$id                     = $app->input->getInt('id');

$showModal = false;

JHtml::_('jquery.framework');
JHtml::script(JUri::root() . '/media/com_osdownloads/js/jquery.osdownloads.bundle.min.js');

?>
<form action="<?php echo(JRoute::_($container->helperRoute->getFileListRoute($id, $itemId)));?>" method="post" name="adminForm" id="adminForm">
    <div class="contentopen osdownloads-container">
        <?php if ($this->showCategoryFilter && !empty($this->categories)) : ?>
            <div class="category_filter columns-<?php echo $numberOfColumns; ?>">
                <?php
                $i = 0;
                foreach ($this->categories as $category) : ?>
                    <?php if (in_array($category->access, $authorizedAccessLevels)) : ?>
                        <div class="column column-<?php echo $i % $numberOfColumns; ?> item<?php echo($i % $numberOfColumns);?> cate_<?php echo($category->id);?>">
                            <h3>
                                <a href="<?php echo(JRoute::_($container->helperRoute->getFileListRoute($category->id, $itemId)));?>">
                                    <?php echo($category->title);?>
                                </a>
                            </h3>
                            <div class="item_content">
                                <?php echo($category->description);?>
                            </div>
                        </div>
                        <?php if ($numberOfColumns && $i % $numberOfColumns == $numberOfColumns - 1) : ?>
                            <div class="seperator"></div>
                            <div class="clr"></div>
                        <?php endif;?>
                        <?php $i++;?>
                    <?php endif; ?>
                <?php endforeach;?>
                <div class="clr"></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($this->items)) : ?>
            <?php foreach ($this->items as $file) : ?>
                <?php
                $requireEmail = $file->require_user_email;
                $requireAgree = (bool) $file->require_agree;
                $requireShare = (bool) @$file->require_share;

                if (!$showModal) {
                    $showModal = $requireEmail || $requireAgree || $requireShare;
                }

                ?>
                <?php if (in_array($file->access, $authorizedAccessLevels)) : ?>
                    <div class="item_<?php echo $file->id;?>">
                        <h3><a href="<?php echo JRoute::_($container->helperRoute->getViewItemRoute($file->id, $itemId));?>"><?php echo($file->name);?></a></h3>
                        <div class="item_content"><?php echo($file->brief);?></div>

                        <?php if ($this->params->get('show_download_button', 0)) : ?>
                            <div class="osdownloadsactions">
                                <div class="btn_download">
                                    <?php
                                    $fileURL = JRoute::_($container->helperRoute->getViewItemRoute($file->id, $itemId));
                                    $link    = JRoute::_($container->helperRoute->getFileDownloadContentRoute($file->id, $itemId));
                                    ?>
                                    <a
                                        href="<?php echo $link; ?>"
                                        class="osdownloadsDownloadButton"
                                        style="background:<?php echo $file->download_color;?>"
                                        data-direct-page="<?php echo $file->direct_page; ?>"
                                        data-require-email="<?php echo $requireEmail; ?>"
                                        data-require-agree="<?php echo $requireAgree ? 1 : 0; ?>"
                                        data-require-share="<?php echo $requireShare ? 1 : 0; ?>"
                                        data-id="<?php echo $file->id; ?>"
                                        data-url="<?php echo $fileURL; ?>"
                                        data-lang="<?php echo $lang->getTag(); ?>"
                                        data-name="<?php echo $file->name; ?>"
                                        data-agreement-article="<?php echo $file->agreementLink; ?>"
                                        <?php if ($this->isPro) : ?>
                                            data-hashtags="<?php echo str_replace('#', '', @$file->twitter_hashtags); ?>"
                                            data-via="<?php echo str_replace('@', '', @$file->twitter_via); ?>"
                                        <?php endif; ?>
                                        >
                                        <span>
                                            <?php echo $this->params->get('link_label', JText::_('COM_OSDOWNLOADS_DOWNLOAD')); ?>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($this->params->get('show_readmore_button', 1)) : ?>
                            <div class="osdownloads-readmore-wrapper readmore_wrapper">
                                <div class="osdownloads-readmore readmore">
                                    <a href="<?php echo JRoute::_($container->helperRoute->getViewItemRoute($file->id, $itemId));?>">
                                        <?php echo(JText::_("COM_OSDOWNLOADS_READ_MORE"));?>
                                    </a>
                                </div>
                                <div class="clr"></div>
                            </div>
                        <?php endif; ?>
                        <div class="seperator"></div>
                    </div>
                <?php endif; ?>
            <?php endforeach;?>
        <?php else : ?>
            <div class="osd-alert">
                <?php echo JText::_('COM_OSDOWNLOADS_NO_DOWNLOADS'); ?>
            </div>
        <?php endif; ?>

        <div class="clr"></div>
        <div class="osdownloads-pages-counter"><?php echo $this->pagination->getPagesCounter(); ?></div>
        <div class="osdownloads-pagination"><?php echo $this->pagination->getPagesLinks(); ?></div>
    </div>

</form>

<?php if ($this->params->get('show_download_button', 1)) : ?>
    <div id="osdownloadsRequirementsPopup" class="reveal-modal osdownloads-modal <?php echo AllediaHelper::getJoomlaVersionCssClass(); ?>">
        <h2 class="title"><?php echo JText::_('COM_OSDOWNLOADS_BEFORE_DOWNLOAD'); ?></h2>

        <div id="osdownloadsEmailGroup" class="osdownloadsemail" style="display: none;">

            <label for="osdownloadsRequireEmail">
                <input type="email" aria-required="true" required name="require_email" id="osdownloadsRequireEmail" placeholder="<?php echo JText::_("COM_OSDOWNLOADS_ENTER_EMAIL_ADDRESS"); ?>" />
            </label>

            <div class="error" style="display: none;" id="osdownloadsErrorInvalidEmail">
                <?php echo JText::_("COM_OSDOWNLOADS_INVALID_EMAIL"); ?>
            </div>
        </div>

        <div id="osdownloadsAgreeGroup" class="osdownloadsagree" style="display: none;">
            <label for="osdownloadsRequireAgree">
                <input type="checkbox" name="require_agree" id="osdownloadsRequireAgree" value="1" />
                <span>
                    * <?php echo(JText::_("COM_OSDOWNLOADS_DOWNLOAD_TERM"));?>
                </span>
            </label>

            <div class="error" style="display: none;" id="osdownloadsErrorAgreeTerms">
                <?php echo JText::_("COM_OSDOWNLOADS_YOU_HAVE_AGREE_TERMS_TO_DOWNLOAD_THIS"); ?>
            </div>
        </div>

        <?php if ($this->isPro) : ?>
            <div id="osdownloadsShareGroup" class="osdownloadsshare" style="display: none;">
                <!-- Facebook -->
                <div id="fb-root"></div>

                <p id="osdownloadsRequiredShareMessage" style="display: none;">
                    <?php echo JText::_('COM_OSDOWNLOADS_YOU_MUST_TWEET_SHARE_FACEBOOK'); ?>
                </p>

                <div class="error" style="display: none;" id="osdownloadsErrorShare">
                    <?php echo JText::_("COM_OSDOWNLOADS_SHARE_TO_DOWNLOAD_THIS"); ?>
                </div>
            </div>

        <?php endif; ?>

        <a href="#"  id="osdownloadsDownloadContinue" class="osdownloads-readmore readmore">
            <span>
                <?php echo JText::_("COM_OSDOWNLOADS_CONTINUE"); ?>
            </span>
        </a>

        <a class="close-reveal-modal">&#215;</a>
    </div>

    <script>
    (function ($) {

        $(function osdownloadsDomReady() {
            $('.osdownloads-container .osdownloadsDownloadButton').osdownloads({
                animation: '<?php echo $this->params->get("popup_animation", "fade"); ?>',
                elementsPrefix: 'osdownloads',
                popupElementId: 'osdownloadsRequirementsPopup'
            });
        });

    })(jQuery);
    </script>

<?php endif;?>
