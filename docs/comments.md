# Profile Comments

Persona supports profile comments with two levels:

```text
Top-level comment
└── Reply
```

Replies cannot receive additional replies.

## Configuration

```php
'comments' => [
    'enabled' => true,
    'require_approval' => false,
    'allow_guest_comments' => false,
    'max_length' => 1000,
    'replies_enabled' => true,
],
```

Persona provides model-level comment behavior. Your application is responsible for authentication, authorization, request validation, rate limiting, spam protection, and any guest-comment workflow.

## Add a comment

```php
$comment = $profile->addComment(
    $user,
    'Great profile.'
);
```

From a model using `HasPersona`:

```php
$comment = $user->commentOnPersona(
    $profile,
    'Great profile.'
);
```

Comments are approved automatically unless `require_approval` is enabled.

## Add a reply

```php
$reply = $comment->addReply(
    $otherUser,
    'Thank you.'
);
```

Calling `addReply()` on an existing reply throws a `LogicException`.

## Relationships

```php
$profile->comments;

$comment->persona;

$comment->user;

$comment->parent;

$comment->replies;
```

## Query top-level comments

```php
$comments = $profile
    ->approvedComments()
    ->latest()
    ->get();
```

Pinned comments:

```php
$pinned = $profile
    ->pinnedComments()
    ->latest()
    ->get();
```

Load approved replies with each comment:

```php
$comments = $profile
    ->approvedComments()
    ->with([
        'user',
        'replies' => fn ($query) => $query
            ->approved()
            ->oldest(),
        'replies.user',
    ])
    ->latest()
    ->get();
```

## Comment scopes

```php
use EloquentWorks\Persona\Models\PersonaComment;

PersonaComment::approved()->get();

PersonaComment::pinned()->get();

PersonaComment::topLevel()->get();

PersonaComment::repliesOnly()->get();
```

## Comment helpers

```php
$comment->isTopLevel();

$comment->isReply();
```

## Approval

```php
$comment->approve();

$comment->unapprove();
```

## Pinning

```php
$comment->pin();

$comment->unpin();
```

Pinning is intended mainly for top-level profile comments.

## Editing

```php
$comment->edit('Updated comment body.');
```

`edit()` trims the body, rejects an empty value, enforces the configured maximum length, and updates `edited_at`.

## Deleting comment threads

Persona comments use soft deletes.

Soft-delete a parent comment and its replies:

```php
$comment->delete();
```

Permanently delete a parent comment and all replies, including previously soft-deleted replies:

```php
$comment->forceDelete();
```

## Authorization

Persona does not decide who may edit, delete, approve, pin, or reply to comments. The consuming application should enforce policies such as:

- users may edit or delete their own comments
- profile owners may moderate comments on their profiles
- administrators may approve, pin, or remove comments
- unauthenticated users may not post unless the application explicitly supports it
