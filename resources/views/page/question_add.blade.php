<div class="question-add container" ng-controller="QuestionAddController">
  <div class="card">
    <form name="question_add_form" ng-submit="Question.add()">
      <div class="input-group">
        <label>问题标题</label>
        <input
                name="title"
                type="text"
                ng-maxlength="255"
                ng-minlength="5"
                ng-model="Question.new_question.title"
                required>
      </div>
      <div class="input-group">
        <label>问题描述</label>
        <textarea name="desc" ng-model="Question.new_question.desc" required>

          </textarea>
      </div>

      <div class="input-group">
        <button class="primary" ng-disabled="question_add_form.$invalid" type="submit">提交</button>
      </div>
    </form>

  </div>
</div>