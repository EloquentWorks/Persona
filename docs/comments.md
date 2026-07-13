# Profile Comments

Persona supports comments on profiles with a maximum of two levels:

```text
Top-level comment
└── Reply
```

Replies cannot receive additional replies.

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

Comments are approved automatically unless `persona.comments.require_approval` is enabled.

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

## Profile comment queries

Get approved top-level comments:

```php
$comments = $profile
    ->approvedComments()
    ->latest()
    ->get();
```

Get pinned top-level comments:

```php
$pinned = $profile
    ->pinnedComments()
    ->latest()
    ->get();
```

Load approved comments and their approved replies:

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

## Helpers

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

Pinning is intended primarily for top-level profile comments.

## Editing

```php
$comment->edit('Updated comment body.');
```

The `edited_at` timestamp is updated when `edit()` is called.

## Deleting comment threads

The model uses soft deletes. Deleting a top-level comment soft-deletes its direct replies:

```php
$comment->delete();
```

Force-deleting a top-level comment permanently removes its replies, including replies that were already soft-deleted:

```php
$comment->forceDelete();
```

## Validation and authorization

The model validates empty bodies and the configured maximum length. Your application should still enforce:

- authentication
- ownership and update/delete authorization
- profile-owner moderation permissions
- rate limiting
- spam protection
- request validation

Persona provides the model API; it does not register comment-management web routes.
