<?php
function gatherStats($blocksRepository, $pagesRepository, $menuRepository, $postsRepository) {
    $stats = [];

    try {
        $stats['blocks'] = method_exists($blocksRepository, 'countAll') ? $blocksRepository->countAll() : 0;
        $stats['pages']  = method_exists($pagesRepository, 'countAll') ? $pagesRepository->countAll() : 0;
        $stats['menu']   = method_exists($menuRepository, 'countAll') ? $menuRepository->countAll() : 0;
        $stats['posts']  = method_exists($postsRepository, 'countAll') ? $postsRepository->countAll() : 0;
    } catch (Exception $e) {
        error_log("gatherStats() error: " . $e->getMessage());
    }

    return $stats;
}
?>
