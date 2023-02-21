const targetPage = '/'

describe('Root Page Test', () => {
  // テストごとに指定のページへ移動
  beforeEach(() => {
    cy.visit(targetPage)
  })

  it('Visits Root Page.', () => {
    cy.visit(targetPage)

    // cy.get('.p-button-raised').should('be.disabled')
    // cy.get('.p-button-raised').click('center')

    // ヘッダーのメッセージチェック
    cy.get('.page-container').contains('h1', 'サンプル ページ')
  })
})
