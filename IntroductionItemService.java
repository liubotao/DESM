@Service 
 public class IntroductionItemService { @Autowired 
 private IntroductionItemDao introductionItemDao; 
public List<IntroductionItem> getIntroductionItemList (HashMap map) { return introductionItemDao.getIntroductionItemList (HashMap map); }public List<IntroductionItem> getSearchResult (HashMap map) { return introductionItemDao.getSearchResult(map); }public IntroductionItem findIntroductionItemByIntroductionItemId(int introductionItemId) { return introductionItemDao.findIntroductionItemByIntroductionItemId(introductionItemId); }public void delete(int introductionItemId) { introductionItemDao.delete(introductionItemId); }public void deleteByPkIds(List<Integer> ids) { introductionItemDao.deleteByPkIds(ids); } public void update(IntroductionItem introductionItem){ introductionItemDao.update(introductionItem); }public int insert(IntroductionItem introductionItem) { return introductionItemDao.insert(introductionItem);}public int getCount(IntroductionItem introductionItem) { return introductionItemDao.getCount(introductionItem); }public int getTotal() { return introductionItemDao.getTotal(); }}