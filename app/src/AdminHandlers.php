<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function handlePagesPost(string $action, ?PagesRepository $pagesRepository = null): array
{
    return handleContentPost(
        $action,
        $pagesRepository ?? new PagesRepository(),
        function () {
            $slug = get_post_value('slug');
            $title = get_post_value('title');
            $body = get_post_value('body_html', true);
            if ($slug === '' || $title === '' || $body === '') {
                throw new InvalidArgumentException('Missing required page fields');
            }
            return [$slug, $title, $body];
        },
        function (PagesRepository $repo, string $slug, string $title, string $body) {
            return $repo->create($slug, $title, $body);
        },
        function (PagesRepository $repo, int $id, string $slug, string $title, string $body): void {
            $repo->update($id, $slug, $title, $body);
        },
        function (PagesRepository $repo, int $id): void {
            $repo->delete($id);
        },
        'page'
    );
}

function handleBlogPost(string $action, ?PostsRepository $postsRepository = null): array
{
    return handleContentPost(
        $action,
        $postsRepository ?? new PostsRepository(),
        function () {
            $slug = get_post_value('slug');
            $title = get_post_value('title');
            $body = get_post_value('body_html', true);
            if ($slug === '' || $title === '' || $body === '') {
                throw new InvalidArgumentException('Missing required post fields');
            }
            $publishedAtRaw = get_post_value('published_at');
            $publishedAt = $publishedAtRaw !== '' ? (new DateTimeImmutable($publishedAtRaw))->format(DateTimeInterface::ATOM) : null;
            return [$slug, $title, $body, $publishedAt];
        },
        function (PostsRepository $repo, string $slug, string $title, string $body, ?string $publishedAt) {
            return $repo->create($slug, $title, $body, $publishedAt);
        },
        function (PostsRepository $repo, int $id, string $slug, string $title, string $body, ?string $publishedAt): void {
            $repo->update($id, $slug, $title, $body, $publishedAt);
        },
        function (PostsRepository $repo, int $id): void {
            $repo->delete($id);
        },
        'post'
    );
}

function handleMenuPost(string $action, ?MenuRepository $menuRepository = null): array
{
    return handleContentPost(
        $action,
        $menuRepository ?? new MenuRepository(),
        function () {
            $label = get_post_value('label');
            $url = get_post_value('url');
            $positionRaw = get_post_value('position');
            if ($label === '' || $url === '' || $positionRaw === '' || !is_numeric($positionRaw)) {
                throw new InvalidArgumentException('Missing required menu fields');
            }
            $position = (int)$positionRaw;
            return [$label, $url, $position];
        },
        function (MenuRepository $repo, string $label, string $url, int $position) {
            return $repo->create($label, $url, $position);
        },
        function (MenuRepository $repo, int $id, string $label, string $url, int $position): void {
            $repo->update($id, $label, $url, $position);
        },
        function (MenuRepository $repo, int $id): void {
            $repo->delete($id);
        },
        'menu item'
    );
}

function handleBlocksPost(string $action, ?BlocksRepository $blocksRepository = null): array
{
    return handleContentPost(
        $action,
        $blocksRepository ?? new BlocksRepository(),
        function () {
            $identifier = get_post_value('identifier');
            $body = get_post_value('body_html', true);
            if ($identifier === '' || $body === '') {
                throw new InvalidArgumentException('Missing required block fields');
            }
            return [$identifier, $body];
        },
        function (BlocksRepository $repo, string $identifier, string $body) {
            return $repo->create($identifier, $body);
        },
        function (BlocksRepository $repo, int $id, string $identifier, string $body): void {
            $repo->update($id, $identifier, $body);
        },
        function (BlocksRepository $repo, int $id): void {
            $repo->delete($id);
        },
        'content block'
    );
}

/**
 * @template TRepo
 * @param callable():array $collectData
 * @param callable(TRepo,...):int $createCallback
 * @param callable(TRepo,int,...):void $updateCallback
 * @param callable(TRepo,int):void $deleteCallback
 */
function handleContentPost(string $action, $repository, callable $collectData, callable $createCallback, callable $updateCallback, callable $deleteCallback, string $entityLabel): array
{
    require_post_csrf();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new RuntimeException('Invalid request method');
    }

    $action = strtolower(trim($action));
    $response = ['success' => false, 'action' => $action, 'entity' => $entityLabel];

    try {
        switch ($action) {
            case 'create':
                $data = $collectData();
                $id = $createCallback($repository, ...$data);
                $response['success'] = true;
                $response['id'] = $id;
                logLine('INFO', sprintf('Created %s #%d', $entityLabel, $id));
                break;
            case 'update':
                $id = (int)get_post_value('id');
                if ($id <= 0) {
                    throw new InvalidArgumentException('Invalid identifier for update');
                }
                $data = $collectData();
                $updateCallback($repository, $id, ...$data);
                $response['success'] = true;
                $response['id'] = $id;
                logLine('INFO', sprintf('Updated %s #%d', $entityLabel, $id));
                break;
            case 'delete':
                $id = (int)get_post_value('id');
                if ($id <= 0) {
                    throw new InvalidArgumentException('Invalid identifier for delete');
                }
                $deleteCallback($repository, $id);
                $response['success'] = true;
                $response['id'] = $id;
                logLine('INFO', sprintf('Deleted %s #%d', $entityLabel, $id));
                break;
            default:
                throw new InvalidArgumentException('Unsupported action');
        }
    } catch (Throwable $exception) {
        logLine('ERROR', sprintf('Failed to process %s action "%s": %s', $entityLabel, $action, $exception->getMessage()));
        $response['error'] = $exception->getMessage();
    }

    return $response;
}
