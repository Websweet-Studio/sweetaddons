(function ($) {
  function insertAtCursor(input, text) {
    var start = input.selectionStart || 0;
    var end = input.selectionEnd || 0;
    var val = input.value;
    input.value = val.substring(0, start) + text + val.substring(end);
    var pos = start + text.length;
    input.selectionStart = input.selectionEnd = pos;
    input.focus();
  }

  function getAceEditorForTextarea($ta) {
    try {
      var $editorDiv = $ta.prev(".ace_editor");
      if ($editorDiv.length && window.ace) {
        return ace.edit($editorDiv.get(0));
      }
    } catch (e) {}
    return null;
  }

  function ensureToolbar($ta) {
    if ($ta.next(".sap-token-toolbar").length) return;
    var $toolbar = $('<div class="sap-token-toolbar"></div>');
    var tokens = [
      { label: "[link]", token: "[link]" },
      { label: "[title]", token: "[title]" },
      { label: "[excerpt]", token: "[excerpt]" },
      { label: "[date]", token: "[date]" },
      { label: "[date:Y-m-d]", token: "[date:Y-m-d]" },
      { label: "[date:F j, Y]", token: "[date:F j, Y]" },
      { label: "[date:d/m/Y]", token: "[date:d/m/Y]" },
      { label: "[author]", token: "[author]" },
      { label: "[image]", token: "[image]" },
      { label: "[image_url]", token: "[image_url]" },
      { label: "[read_more]", token: "[read_more]" },
      { label: "[meta]", token: "[meta]" },
    ];
    tokens.forEach(function (t) {
      var $btn = $('<a href="#" class="button button-small"></a>').text(
        t.label
      );
      $btn.on("click", function (e) {
        e.preventDefault();
        var editor = getAceEditorForTextarea($ta);
        if (editor) {
          editor.session.insert(editor.getCursorPosition(), t.token);
          editor.focus();
        } else {
          insertAtCursor($ta.get(0), t.token);
        }
      });
      $toolbar.append($btn);
    });

    var snippets = [
      { label: "Title Link", code: '<h3><a href="[link]">[title]</a></h3>' },
      {
        label: "Image Link",
        code: '<a href="[link]"><img src="[image_url]" alt="[title]"></a>',
      },
      {
        label: "Meta",
        code: '<div class="sap-meta">[date:F j, Y] · [author]</div>',
      },
      { label: "Excerpt", code: "<p>[excerpt]</p>" },
      {
        label: "Read More",
        code: '<a class="sap-read-more" href="[link]">Baca selengkapnya</a>',
      },
    ];
    snippets.forEach(function (s) {
      var $btn = $('<a href="#" class="button button-small"></a>').text(
        s.label
      );
      $btn.on("click", function (e) {
        e.preventDefault();
        var editor = getAceEditorForTextarea($ta);
        if (editor) {
          editor.session.insert(editor.getCursorPosition(), s.code);
          editor.focus();
        } else {
          insertAtCursor($ta.get(0), s.code);
        }
      });
      $toolbar.append($btn);
    });

    var defaultTpl =
      '<article class="sap-card card h-100">' +
      "[image]" +
      '<div class="sap-body card-body">' +
      '<h3 class="card-title"><a href="[link]" class="stretched-link text-decoration-none">[title]</a></h3>' +
      "[meta]" +
      '<p class="card-text">[excerpt]</p>' +
      "[read_more]" +
      "</div>" +
      "</article>";
    var $insertDefault = $('<a href="#" class="button button-small"></a>').text(
      "Insert Default Layout"
    );
    $insertDefault.on("click", function (e) {
      e.preventDefault();
      var editor = getAceEditorForTextarea($ta);
      if (editor) {
        editor.session.insert(editor.getCursorPosition(), defaultTpl);
        editor.focus();
      } else {
        insertAtCursor($ta.get(0), defaultTpl);
      }
    });
    $toolbar.append($insertDefault);

    var $editorDiv = $ta.prev(".ace_editor");
    if ($editorDiv.length) {
      $editorDiv.before($toolbar);
    } else {
      $ta.before($toolbar);
    }
  }

  function init() {
    var $ta = $('textarea[name="custom_layout_html"]');
    if ($ta.length) {
      ensureToolbar($ta);
    }

    var $taxonomy = $('select[name="taxonomy"]');
    var $term = $('select[name="term"]');
    function fetchTaxRestBase(taxSlug) {
      return $.getJSON("/wp-json/wp/v2/taxonomies")
        .then(function (res) {
          if (res && res[taxSlug] && res[taxSlug].rest_base) {
            return res[taxSlug].rest_base;
          }
          return taxSlug;
        })
        .catch(function () {
          return taxSlug;
        });
    }
    function loadTerms(taxSlug) {
      fetchTaxRestBase(taxSlug)
        .then(function (restBase) {
          return $.getJSON(
            "/wp-json/wp/v2/" + restBase + "?per_page=100&_fields=id,name"
          );
        })
        .then(function (items) {
          if (!$term.length) return;
          var current = $term.val();
          $term.empty();
          $term.append($("<option>").attr("value", "all").text("Alls"));
          $term.append($("<option>").attr("value", "0").text("— Select —"));
          if (Array.isArray(items)) {
            items.forEach(function (it) {
              var opt = $("<option>").attr("value", it.id).text(it.name);
              $term.append(opt);
            });
          }
          if (current && $term.find('option[value="' + current + '"]').length) {
            $term.val(current);
          }
        });
    }
    if ($taxonomy.length && $term.length) {
      loadTerms($taxonomy.val());
      $taxonomy.on("change", function () {
        loadTerms($(this).val());
      });
    }
  }

  $(document).ready(init);
  $(document).on("fl-builder-settings-init fl-builder-settings-rendered", init);
})(jQuery);
