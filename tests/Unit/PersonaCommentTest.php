<?php

namespace EloquentWorks\Persona\Tests\Unit;

use EloquentWorks\Persona\Models\PersonaComment;
use EloquentWorks\Persona\Tests\TestCase;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\Attributes\Test;

class PersonaCommentTest extends TestCase
{
    #[Test]
    public function it_can_add_a_top_level_comment_to_a_persona(): void
    {
        $profileOwner = createUser(['name' => 'Profile Owner']);
        $commenter = createUser(['name' => 'Commenter']);

        $persona = $profileOwner->createPersona([
            'slug' => 'profile-owner',
        ]);

        $comment = $persona->addComment(
            $commenter,
            'Great profile.'
        );

        $this->assertInstanceOf(PersonaComment::class, $comment);
        $this->assertSame($persona->getKey(), $comment->persona_id);
        $this->assertSame($commenter->getKey(), $comment->user_id);
        $this->assertSame('Great profile.', $comment->body);
        $this->assertNull($comment->parent_id);
        $this->assertTrue($comment->isTopLevel());
        $this->assertFalse($comment->isReply());

        $this->assertDatabaseHas('persona_comments', [
            'id' => $comment->getKey(),
            'persona_id' => $persona->getKey(),
            'parent_id' => null,
            'user_id' => $commenter->getKey(),
            'body' => 'Great profile.',
        ]);
    }

    #[Test]
    public function it_can_add_a_reply_to_a_top_level_comment(): void
    {
        $profileOwner = createUser(['name' => 'Profile Owner']);
        $commenter = createUser(['name' => 'Commenter']);
        $replier = createUser(['name' => 'Replier']);

        $persona = $profileOwner->createPersona([
            'slug' => 'profile-owner',
        ]);

        $comment = $persona->addComment(
            $commenter,
            'Nice work.'
        );

        $reply = $comment->addReply(
            $replier,
            'Thank you.'
        );

        $this->assertSame($comment->getKey(), $reply->parent_id);
        $this->assertSame($persona->getKey(), $reply->persona_id);
        $this->assertSame($replier->getKey(), $reply->user_id);
        $this->assertSame('Thank you.', $reply->body);
        $this->assertTrue($reply->isReply());
        $this->assertFalse($reply->isTopLevel());

        $this->assertTrue(
            $reply->parent->is($comment)
        );

        $this->assertTrue(
            $comment->replies->contains($reply)
        );
    }

    #[Test]
    public function a_reply_cannot_receive_another_reply(): void
    {
        $profileOwner = createUser(['name' => 'Profile Owner']);
        $firstUser = createUser(['name' => 'First User']);
        $secondUser = createUser(['name' => 'Second User']);
        $thirdUser = createUser(['name' => 'Third User']);

        $persona = $profileOwner->createPersona([
            'slug' => 'profile-owner',
        ]);

        $comment = $persona->addComment(
            $firstUser,
            'Top-level comment.'
        );

        $reply = $comment->addReply(
            $secondUser,
            'First-level reply.'
        );

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'Replies cannot contain nested replies.'
        );

        $reply->addReply(
            $thirdUser,
            'This third level must fail.'
        );
    }

    #[Test]
    public function top_level_scope_excludes_replies(): void
    {
        $profileOwner = createUser(['name' => 'Profile Owner']);
        $commenter = createUser(['name' => 'Commenter']);
        $replier = createUser(['name' => 'Replier']);

        $persona = $profileOwner->createPersona([
            'slug' => 'profile-owner',
        ]);

        $comment = $persona->addComment(
            $commenter,
            'Top-level comment.'
        );

        $comment->addReply(
            $replier,
            'Reply.'
        );

        $topLevelComments = PersonaComment::topLevel()->get();

        $this->assertCount(1, $topLevelComments);
        $this->assertTrue($topLevelComments->first()?->is($comment));
    }

    #[Test]
    public function replies_only_scope_excludes_top_level_comments(): void
    {
        $profileOwner = createUser(['name' => 'Profile Owner']);
        $commenter = createUser(['name' => 'Commenter']);
        $replier = createUser(['name' => 'Replier']);

        $persona = $profileOwner->createPersona([
            'slug' => 'profile-owner',
        ]);

        $comment = $persona->addComment(
            $commenter,
            'Top-level comment.'
        );

        $reply = $comment->addReply(
            $replier,
            'Reply.'
        );

        $replies = PersonaComment::repliesOnly()->get();

        $this->assertCount(1, $replies);
        $this->assertTrue($replies->first()?->is($reply));
    }

    #[Test]
    public function approved_persona_comments_only_return_top_level_comments(): void
    {
        $profileOwner = createUser(['name' => 'Profile Owner']);
        $commenter = createUser(['name' => 'Commenter']);
        $replier = createUser(['name' => 'Replier']);

        $persona = $profileOwner->createPersona([
            'slug' => 'profile-owner',
        ]);

        $comment = $persona->addComment(
            $commenter,
            'Top-level comment.'
        );

        $comment->addReply(
            $replier,
            'Approved reply.'
        );

        $approvedComments = $persona
            ->approvedComments()
            ->get();

        $this->assertCount(1, $approvedComments);
        $this->assertTrue(
            $approvedComments->first()?->is($comment)
        );
    }

    #[Test]
    public function deleting_a_parent_comment_deletes_its_replies(): void
    {
        $profileOwner = createUser(['name' => 'Profile Owner']);
        $commenter = createUser(['name' => 'Commenter']);
        $replier = createUser(['name' => 'Replier']);

        $persona = $profileOwner->createPersona([
            'slug' => 'profile-owner',
        ]);

        $comment = $persona->addComment(
            $commenter,
            'Parent comment.'
        );

        $reply = $comment->addReply(
            $replier,
            'Child reply.'
        );

        $comment->forceDelete();

        $this->assertDatabaseMissing('persona_comments', [
            'id' => $comment->getKey(),
        ]);

        $this->assertDatabaseMissing('persona_comments', [
            'id' => $reply->getKey(),
        ]);
    }

    #[Test]
    public function it_trims_the_body_when_editing_a_comment(): void
    {
        $owner = createUser();
        $commenter = createUser();

        $persona = $owner->createPersona([
            'slug' => 'owner',
        ]);

        $comment = $persona->addComment(
            $commenter,
            'Original comment.'
        );

        $comment->edit('  Updated comment.  ');

        $comment->refresh();

        $this->assertSame('Updated comment.', $comment->body);
        $this->assertNotNull($comment->edited_at);
    }

    #[Test]
    public function a_comment_cannot_be_edited_to_an_empty_body(): void
    {
        $owner = createUser();
        $commenter = createUser();

        $persona = $owner->createPersona([
            'slug' => 'owner',
        ]);

        $comment = $persona->addComment(
            $commenter,
            'Original comment.'
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Comment body cannot be empty.'
        );

        $comment->edit('   ');
    }

    #[Test]
    public function a_comment_edit_cannot_exceed_the_maximum_length(): void
    {
        config()->set('persona.comments.max_length', 10);

        $owner = createUser();
        $commenter = createUser();

        $persona = $owner->createPersona([
            'slug' => 'owner',
        ]);

        $comment = $persona->addComment(
            $commenter,
            'Original.'
        );

        $this->expectException(InvalidArgumentException::class);

        $comment->edit('This comment is too long.');
    }

    #[Test]
    public function replies_respect_the_comment_approval_setting(): void
    {
        config()->set(
            'persona.comments.require_approval',
            true
        );

        $profileOwner = createUser(['name' => 'Profile Owner']);
        $commenter = createUser(['name' => 'Commenter']);
        $replier = createUser(['name' => 'Replier']);

        $persona = $profileOwner->createPersona([
            'slug' => 'profile-owner',
        ]);

        $comment = $persona->addComment(
            $commenter,
            'Pending comment.'
        );

        $reply = $comment->addReply(
            $replier,
            'Pending reply.'
        );

        $this->assertFalse($comment->is_approved);
        $this->assertFalse($reply->is_approved);
    }

    #[Test]
    public function replies_can_be_disabled(): void
    {
        config()->set(
            'persona.comments.replies_enabled',
            false
        );

        $profileOwner = createUser(['name' => 'Profile Owner']);
        $commenter = createUser(['name' => 'Commenter']);
        $replier = createUser(['name' => 'Replier']);

        $persona = $profileOwner->createPersona([
            'slug' => 'profile-owner',
        ]);

        $comment = $persona->addComment(
            $commenter,
            'Top-level comment.'
        );

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'Persona comment replies are disabled.'
        );

        $comment->addReply(
            $replier,
            'This should fail.'
        );
    }

    #[Test]
    public function it_can_approve_and_unapprove_a_comment(): void
    {
        $owner = createUser(['name' => 'Owner']);
        $commenter = createUser(['name' => 'Commenter']);

        $persona = $owner->createPersona([
            'slug' => 'owner',
        ]);

        $comment = $persona->addComment(
            $commenter,
            'Test comment.'
        );

        $comment->unapprove();

        $this->assertFalse($comment->refresh()->is_approved);

        $comment->approve();

        $this->assertTrue($comment->refresh()->is_approved);
    }

    #[Test]
    public function it_can_pin_and_unpin_a_comment(): void
    {
        $owner = createUser(['name' => 'Owner']);
        $commenter = createUser(['name' => 'Commenter']);

        $persona = $owner->createPersona([
            'slug' => 'owner',
        ]);

        $comment = $persona->addComment(
            $commenter,
            'Test comment.'
        );

        $comment->pin();

        $this->assertTrue($comment->refresh()->is_pinned);

        $comment->unpin();

        $this->assertFalse($comment->refresh()->is_pinned);
    }

    #[Test]
    public function it_can_edit_a_comment(): void
    {
        $owner = createUser(['name' => 'Owner']);
        $commenter = createUser(['name' => 'Commenter']);

        $persona = $owner->createPersona([
            'slug' => 'owner',
        ]);

        $comment = $persona->addComment(
            $commenter,
            'Original body.'
        );

        $comment->edit('Updated body.');

        $comment->refresh();

        $this->assertSame('Updated body.', $comment->body);
        $this->assertNotNull($comment->edited_at);
    }
}
